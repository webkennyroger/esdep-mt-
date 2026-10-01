<?php

namespace App\Livewire\Admin;

use App\Models\ActivityLog;
use App\Models\Reservation;
use App\Models\User;
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

    public string $userFilter = '';

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

    private function scopedActivityLogQuery()
    {
        return ActivityLog::with('user');
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

        // Filtro por tipo
        if (! empty($this->typeFilter)) {
            $query->where('subject_type', $this->typeFilter);
        }

        // Filtro por usuário
        if (! empty($this->userFilter)) {
            $query->where('user_id', $this->userFilter);
        }

        // Filtro por data
        if (! empty($this->dateFrom)) {
            $query->whereDate('created_at', '>=', $this->dateFrom);
        }
        if (! empty($this->dateTo)) {
            $query->whereDate('created_at', '<=', $this->dateTo);
        }

        // Contagem por ação (para as pills de filtro), respeitando busca/tipo/usuário/data mas nao a acao em si
        $actionCounts = [
            '' => (clone $query)->count(),
            'created' => (clone $query)->where('action', 'created')->count(),
            'updated' => (clone $query)->where('action', 'updated')->count(),
            'approved' => (clone $query)->where('action', 'approved')->count(),
            'rejected' => (clone $query)->where('action', 'rejected')->count(),
            'deleted' => (clone $query)->where('action', 'deleted')->count(),
        ];

        // Filtro por ação (aplicado por ultimo, para nao afetar os contadores das pills)
        if (! empty($this->actionFilter)) {
            $query->where('action', $this->actionFilter);
        }

        // Usuarios que aparecem no historico, para o filtro "Usuário"
        $users = User::whereIn('id', ActivityLog::query()->whereNotNull('user_id')->distinct()->pluck('user_id'))
            ->orderBy('name')
            ->get(['id', 'name']);

        // Estatísticas (today/thisWeek/reservations e as tendências não dependem dos filtros da tela, então ficam em cache curto)
        $isScoped = ! auth()->user()->is_admin;
        $statsCacheKey = $isScoped ? 'activity-log-fixed-stats:user:'.auth()->id() : 'activity-log-fixed-stats';
        $fixedStats = Cache::remember($statsCacheKey, now()->addMinutes(5), function () {
            $pct = fn (int $current, int $previous): int => $previous > 0
                ? (int) round((($current - $previous) / $previous) * 100)
                : ($current > 0 ? 100 : 0);

            $today = $this->scopedActivityLogQuery()->whereDate('created_at', today())->count();
            $yesterday = $this->scopedActivityLogQuery()->whereDate('created_at', today()->subDay())->count();

            $thisWeek = $this->scopedActivityLogQuery()->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count();
            $lastWeek = $this->scopedActivityLogQuery()->whereBetween('created_at', [now()->subWeek()->startOfWeek(), now()->subWeek()->endOfWeek()])->count();

            $thisMonth = $this->scopedActivityLogQuery()->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])->count();
            $lastMonth = $this->scopedActivityLogQuery()->whereBetween('created_at', [now()->subMonth()->startOfMonth(), now()->subMonth()->endOfMonth()])->count();

            $thisMonthReservations = $this->scopedActivityLogQuery()->where('subject_type', Reservation::class)->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])->count();
            $lastMonthReservations = $this->scopedActivityLogQuery()->where('subject_type', Reservation::class)->whereBetween('created_at', [now()->subMonth()->startOfMonth(), now()->subMonth()->endOfMonth()])->count();

            return [
                'today' => $today,
                'thisWeek' => $thisWeek,
                'reservations' => $this->scopedActivityLogQuery()->where('subject_type', Reservation::class)->count(),
                'todayTrend' => $pct($today, $yesterday),
                'thisWeekTrend' => $pct($thisWeek, $lastWeek),
                'totalTrend' => $pct($thisMonth, $lastMonth),
                'reservationsTrend' => $pct($thisMonthReservations, $lastMonthReservations),
            ];
        });
        $stats = array_merge($fixedStats, ['total' => $actionCounts[''], 'actionCounts' => $actionCounts]);

        return view('livewire.admin.activity-log', [
            'logs' => $query->latest()->paginate(15),
            'stats' => $stats,
            'users' => $users,
            'selectedLog' => $this->getSelectedLogProperty(),
        ]);
    }
}
