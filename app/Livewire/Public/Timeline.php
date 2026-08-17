<?php

namespace App\Livewire\Public;

use App\Enums\ReservationStatus;
use App\Models\Reservation;
use Livewire\Component;

class Timeline extends Component
{
    public int $currentPage = 1;
    public int $perPage = 7;

    public function nextPage(): void
    {
        $totalPages = $this->getTotalPages();
        if ($this->currentPage < $totalPages) {
            $this->currentPage++;
        }
    }

    public function prevPage(): void
    {
        if ($this->currentPage > 1) {
            $this->currentPage--;
        }
    }

    public function getTotalPages(): int
    {
        $total = Reservation::where('status', ReservationStatus::AUTHORIZED->value)
            ->where('date', '>=', now()->toDateString())
            ->count();

        return (int) ceil($total / $this->perPage);
    }

    public function render()
    {
        $query = Reservation::with('room')
            ->where('status', ReservationStatus::AUTHORIZED->value)
            ->where('date', '>=', now()->toDateString())
            ->orderBy('date')
            ->orderBy('start_time');

        $totalPages = $this->getTotalPages();

        if ($this->currentPage > 1) {
            $query->skip(($this->currentPage - 1) * $this->perPage);
        }

        $reservations = $query->limit($this->perPage)->get();

        return view('livewire.public.timeline', [
            'reservations' => $reservations,
            'currentPage' => $this->currentPage,
            'totalPages' => $totalPages,
        ]);
    }
}
