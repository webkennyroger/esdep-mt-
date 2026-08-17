<?php

namespace App\Livewire\Admin;

use App\Models\Reservation;
use App\Models\Room;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.app')]
class Dashboard extends Component
{
    public int $pendingCount = 0;
    public int $authorizedCount = 0;
    public int $rejectedCount = 0;
    public int $roomsCount = 0;
    public array $upcomingReservations = [];
    public array $pendingReservations = [];

    protected $listeners = [
        'reservationCreated' => '$refresh',
        'reservationUpdated' => '$refresh',
        'reservationApproved' => '$refresh',
        'reservationRejected' => '$refresh',
    ];

    public function mount(): void
    {
        $this->loadData();
    }

    public function loadData(): void
    {
        $this->pendingCount = Reservation::where('status', 'pending')->count();
        $this->authorizedCount = Reservation::where('status', 'authorized')->count();
        $this->rejectedCount = Reservation::where('status', 'rejected')->count();
        $this->roomsCount = Room::where('is_active', true)->count();

        $this->upcomingReservations = Reservation::with('room')
            ->where('status', 'authorized')
            ->where('date', '>=', now()->toDateString())
            ->orderBy('date')
            ->orderBy('start_time')
            ->limit(5)
            ->get()
            ->toArray();

        $this->pendingReservations = Reservation::with('room')
            ->where('status', 'pending')
            ->latest()
            ->limit(5)
            ->get()
            ->toArray();
    }

    public function render()
    {
        return view('livewire.admin.dashboard');
    }
}
