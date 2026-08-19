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
        'reservationCreated'  => '$refresh',
        'reservationUpdated'  => '$refresh',
        'reservationApproved' => '$refresh',
        'reservationRejected' => '$refresh',
    ];

    public function render()
    {
        return view('livewire.admin.dashboard', [
            'pendingCount'        => Reservation::where('status', ReservationStatus::PENDING)->count(),
            'authorizedCount'     => Reservation::where('status', ReservationStatus::AUTHORIZED)->count(),
            'rejectedCount'       => Reservation::where('status', ReservationStatus::REJECTED)->count(),
            'roomsCount'          => Room::where('is_active', true)->count(),
            'upcomingReservations' => Reservation::with('room')
                ->where('status', ReservationStatus::AUTHORIZED)
                ->where('date', '>=', now()->toDateString())
                ->orderBy('date')
                ->orderBy('start_time')
                ->limit(5)
                ->get(),
            'pendingReservations' => Reservation::with('room')
                ->where('status', ReservationStatus::PENDING)
                ->latest()
                ->limit(5)
                ->get(),
        ]);
    }
}
