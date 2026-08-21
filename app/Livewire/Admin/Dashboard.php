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
        $statusCounts = (clone $reservations)->selectRaw('status, count(*) as total')->groupBy('status')->pluck('total', 'status');

        return view('livewire.admin.dashboard', [
            'pendingCount' => $statusCounts[ReservationStatus::PENDING->value] ?? 0,
            'authorizedCount' => $statusCounts[ReservationStatus::AUTHORIZED->value] ?? 0,
            'rejectedCount' => $statusCounts[ReservationStatus::REJECTED->value] ?? 0,
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
