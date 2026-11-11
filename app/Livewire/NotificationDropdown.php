<?php

namespace App\Livewire;

use App\Enums\ReservationStatus;
use App\Models\Reservation;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Livewire\Component;

class NotificationDropdown extends Component
{
    public int $pendingCount = 0;

    public array $notifications = [];

    public bool $hasUnread = false;

    protected $listeners = [
        'reservationCreated' => 'loadData',
        'reservationApproved' => 'loadData',
        'reservationRejected' => 'loadData',
        'reservationUpdated' => 'loadData',
    ];

    public function mount(): void
    {
        $this->loadData();
    }

    public function loadData(): void
    {
        if (! auth()->check() || ! auth()->user()->is_admin) {
            $this->pendingCount = 0;
            $this->notifications = [];
            $this->hasUnread = false;

            return;
        }

        $visible = Reservation::accessibleTo(auth()->user());
        $this->pendingCount = (clone $visible)->where('status', ReservationStatus::PENDING)->count();

        $this->notifications = (clone $visible)->with('room')
            ->latest()
            ->take(5)
            ->get()
            ->map(function ($notif) {
                return [
                    'id' => $notif->id,
                    'event_name' => $notif->event_name,
                    'requester_name' => $notif->requester_name,
                    'date' => Carbon::parse($notif->date)->format('d/m'),
                    'status' => $notif->status->value,
                ];
            })
            ->toArray();

        $latestPendingAt = (clone $visible)->where('status', ReservationStatus::PENDING)->max('created_at');
        $seenAt = Cache::get($this->seenCacheKey());
        $this->hasUnread = $latestPendingAt && (! $seenAt || Carbon::parse($latestPendingAt)->gt(Carbon::parse($seenAt)));
    }

    public function markAsSeen(): void
    {
        Cache::put($this->seenCacheKey(), now()->toDateTimeString(), now()->addDays(60));
        $this->hasUnread = false;
    }

    private function seenCacheKey(): string
    {
        return 'admin-notifications-seen-at:'.auth()->id();
    }

    public function goToReservation(int $id): void
    {
        $this->redirect(route('admin.reservations.index'));
    }

    public function render()
    {
        return view('livewire.notification-dropdown');
    }
}
