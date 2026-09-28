<?php

namespace App\Livewire\Admin;

use App\Models\ActivityLog;
use App\Models\Reservation;
use App\Models\Room;
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

    public function mount(): void
    {
        abort_unless(auth()->user()?->can('access-activity-log'), 403);
    }

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

    /**
     * Quem nao e admin/super-admin (ex.: gerenciador) so ve o historico de
     * salas e reservas do proprio setor — nada de contatos, grupos ou usuarios.
     */
    private function scopedActivityLogQuery()
    {
        $query = ActivityLog::with('user');

        if (auth()->user()->is_admin) {
            return $query;
        }

        $roomIds = auth()->user()->managedRoomIds();
        $reservationIds = Reservation::whereIn('room_id', $roomIds)->pluck('id');

        return $query->where(function ($scoped) use ($roomIds, $reservationIds) {
            $scoped->where(fn ($q) => $q->where('subject_type', Room::class)->whereIn('subject_id', $roomIds))
                ->orWhere(fn ($q) => $q->where('subject_type', Reservation::class)->whereIn('subject_id', $reservationIds));
        });
    }

    public function render()
    {
        $query = $this->scopedActivityLogQuery();

        // Filtro por busca
        if (! empty($this->search)) {
            $query->where(function ($q) {
                $q->where('description', 'like', "%{$this->search}%")
                    ->orWhereHas('user', function ($q2) {
                        $q2->where('name', 'like', "%{$this->search}%");
                    });
            });
        }

        // Filtro por ação
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

        // Estatísticas (today/thisWeek/reservations não dependem dos filtros da tela, então ficam em cache curto)
        $isScoped = ! auth()->user()->is_admin;
        $statsCacheKey = $isScoped ? 'activity-log-fixed-stats:user:'.auth()->id() : 'activity-log-fixed-stats';
        $fixedStats = Cache::remember($statsCacheKey, now()->addMinutes(5), function () {
            return [
                'today' => $this->scopedActivityLogQuery()->whereDate('created_at', today())->count(),
                'thisWeek' => $this->scopedActivityLogQuery()->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
                'reservations' => $this->scopedActivityLogQuery()->where('subject_type', Reservation::class)->count(),
            ];
        });
        $stats = array_merge($fixedStats, ['total' => (clone $query)->count()]);

        return view('livewire.admin.activity-log', [
            'logs' => $query->latest()->paginate(15),
            'stats' => $stats,
            'selectedLog' => $this->getSelectedLogProperty(),
        ]);
    }
}
