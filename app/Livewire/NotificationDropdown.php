<?php

namespace App\Livewire;

use App\Enums\ReservationStatus;
use App\Models\Reservation;
use Carbon\Carbon;
use Livewire\Component;

class NotificationDropdown extends Component
{
    public int $pendingCount = 0;
    public array $notifications = [];

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
        $this->pendingCount = Reservation::where('status', ReservationStatus::PENDING)->count();

        $this->notifications = Reservation::with('room')
            ->latest()
            ->take(5)
            ->get()
            ->map(function ($notif) {
                return [
                    'id' => $notif->id,
                    'event_name' => $notif->event_name,
                    'requester_name' => $notif->requester_name,
                    'date' => \Carbon\Carbon::parse($notif->date)->format('d/m'),
                    'status' => $notif->status->value,
                ];
            })
            ->toArray();
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
