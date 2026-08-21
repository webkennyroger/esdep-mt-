<?php

namespace App\Livewire\Contact;

use App\Models\ActivityLog;
use App\Models\Reservation;
use App\Models\Room;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.contact', ['title' => 'Histórico de Atividades — ESDEP-MT'])]
class ContactActivity extends Component
{
    use WithPagination;

    public string $search = '';

    public string $actionFilter = '';

    public string $typeFilter = '';

    public string $dateFrom = '';

    public string $dateTo = '';

    public int $selectedId = 0;

    public bool $showDetailModal = false;

    public function setFilter(string $filter): void
    {
        $this->actionFilter = $filter;
        $this->resetPage();
    }

    public function setTypeFilter(string $type): void
    {
        $this->typeFilter = $type;
        $this->resetPage();
    }

    public function openDetailModal(int $id): void
    {
        $this->scopedQuery()->findOrFail($id);
        $this->selectedId = $id;
        $this->showDetailModal = true;
    }

    public function closeDetailModal(): void
    {
        $this->selectedId = 0;
        $this->showDetailModal = false;
    }

    private function scopedQuery(): Builder
    {
        $roomIds = auth('contact')->user()->managedRoomIds();
        $reservationIds = Reservation::whereIn('room_id', $roomIds)->select('id');

        return ActivityLog::with('user')->where(fn ($query) => $query
            ->where(fn ($logs) => $logs->where('subject_type', Reservation::class)->whereIn('subject_id', $reservationIds))
            ->orWhere(fn ($logs) => $logs->where('subject_type', Room::class)->whereIn('subject_id', $roomIds)));
    }

    public function render()
    {
        $query = $this->scopedQuery();
        if ($this->search !== '') {
            $query->where('description', 'like', "%{$this->search}%");
        }
        if ($this->actionFilter !== '') {
            $query->where('action', $this->actionFilter);
        }
        if ($this->typeFilter !== '') {
            $query->where('subject_type', $this->typeFilter);
        }
        if ($this->dateFrom !== '') {
            $query->whereDate('created_at', '>=', $this->dateFrom);
        }
        if ($this->dateTo !== '') {
            $query->whereDate('created_at', '<=', $this->dateTo);
        }
        $base = $this->scopedQuery();
        $stats = ['total' => (clone $base)->count(), 'today' => (clone $base)->whereDate('created_at', today())->count(), 'thisWeek' => (clone $base)->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(), 'reservations' => (clone $base)->where('subject_type', Reservation::class)->count()];

        return view('livewire.admin.activity-log', ['logs' => $query->latest()->paginate(15), 'stats' => $stats, 'selectedLog' => $this->selectedId ? $this->scopedQuery()->find($this->selectedId) : null]);
    }
}
