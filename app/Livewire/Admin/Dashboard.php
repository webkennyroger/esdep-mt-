<?php

namespace App\Livewire\Admin;

use App\Enums\ReservationStatus;
use App\Models\Reservation;
use App\Models\Room;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class Dashboard extends Component
{
    protected $listeners = [
        'reservationCreated' => '$refresh',
        'reservationUpdated' => '$refresh',
        'reservationApproved' => '$refresh',
        'reservationRejected' => '$refresh',
    ];

    public function render()
    {
        $reservations = Reservation::accessibleTo(auth()->user());
        $rooms = Room::accessibleTo(auth()->user());

        return view('livewire.admin.dashboard', [
            'pendingCount' => (clone $reservations)->where('status', ReservationStatus::PENDING)->count(),
            'authorizedCount' => (clone $reservations)->where('status', ReservationStatus::AUTHORIZED)->count(),
            'rejectedCount' => (clone $reservations)->where('status', ReservationStatus::REJECTED)->count(),
            'roomsCount' => (clone $rooms)->where('is_active', true)->count(),
            'upcomingReservations' => (clone $reservations)->with('room')
                ->where('status', ReservationStatus::AUTHORIZED)
                ->where('date', '>=', now()->toDateString())
                ->orderBy('date')
                ->orderBy('start_time')
                ->limit(5)
                ->get(),
            'pendingReservations' => (clone $reservations)->with('room')
                ->where('status', ReservationStatus::PENDING)
                ->latest()
                ->limit(5)
                ->get(),
        ]);
    }
}
