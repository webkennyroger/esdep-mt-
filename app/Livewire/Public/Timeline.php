<?php

namespace App\Livewire\Public;

use App\Enums\ReservationStatus;
use App\Models\Reservation;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.public')]
class Timeline extends Component
{
    use WithPagination;

    public int $perPage = 7;

    public function prevPage(): void
    {
        $this->previousPage();
    }

    public function render()
    {
        $reservations = Reservation::with('room')
            ->where('status', ReservationStatus::AUTHORIZED)
            ->where('date', '>=', now()->toDateString())
            ->orderBy('date')
            ->orderBy('start_time')
            ->paginate($this->perPage);

        return view('livewire.public.timeline', [
            'reservations' => $reservations,
            'currentPage'  => $reservations->currentPage(),
            'totalPages'   => $reservations->lastPage(),
        ]);
    }
}
