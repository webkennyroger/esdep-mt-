<?php

namespace App\Livewire\Admin;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.app')]
class ActivityLogController extends Component
{
    use WithPagination;

    public string $search = '';

    public string $actionFilter = '';

    public string $typeFilter = '';

    public string $dateFrom = '';

    public string $dateTo = '';

    public int $selectedId = 0;

    public bool $showDetailModal = false;

    protected $listeners = [
        'activityCreated' => '$refresh',
    ];

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
        $this->selectedId = $id;
        $this->showDetailModal = true;
    }

    public function closeDetailModal(): void
    {
        $this->showDetailModal = false;
        $this->selectedId = 0;
    }

    public function getSelectedLogProperty(): ?ActivityLog
    {
        return $this->selectedId ? ActivityLog::with('user')->find($this->selectedId) : null;
    }

    public function render()
    {
        $query = ActivityLog::with('user');

        // Filtro por busca
        if (! empty($this->search)) {
            $query->where(function ($q) {
                $q->where('description', 'like', "%{$this->search}%")
                    ->orWhereHas('user', function ($q2) {
                        $q2->where('name', 'like', "%{$this->search}%");
                    });
            });
        }

        // Filtro por acao
        if (! empty($this->actionFilter)) {
            $query->where('action', $this->actionFilter);
        }

        // Filtro por tipo
        if (! empty($this->typeFilter)) {
            $query->where('subject_type', $this->typeFilter);
        }

        // Filtro por data
        if (! empty($this->dateFrom)) {
            $query->whereDate('created_at', '>=', $this->dateFrom);
        }
        if (! empty($this->dateTo)) {
            $query->whereDate('created_at', '<=', $this->dateTo);
        }

        // Estatisticas (today/thisWeek/reservations nao dependem dos filtros da tela, entao ficam em cache curto)
        $fixedStats = Cache::remember('activity-log-fixed-stats', now()->addMinutes(5), fn () => [
            'today' => ActivityLog::whereDate('created_at', today())->count(),
            'thisWeek' => ActivityLog::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'reservations' => ActivityLog::where('subject_type', 'App\Models\Reservation')->count(),
        ]);
        $stats = array_merge($fixedStats, ['total' => (clone $query)->count()]);

        return view('livewire.admin.activity-log', [
            'logs' => $query->latest()->paginate(15),
            'stats' => $stats,
            'selectedLog' => $this->getSelectedLogProperty(),
        ]);
    }
}
