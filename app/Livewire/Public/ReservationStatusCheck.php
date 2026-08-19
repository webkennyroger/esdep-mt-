<?php

namespace App\Livewire\Public;

use App\Models\Reservation;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.public', ['title' => 'Consultar Reserva — ESDEP-MT'])]
class ReservationStatusCheck extends Component
{
    public string $searchQuery = '';
    public bool $hasSearched = false;

    public function search(): void
    {
        $this->searchQuery = trim($this->searchQuery);
        $this->hasSearched = true;
    }

    public function clear(): void
    {
        $this->searchQuery = '';
        $this->hasSearched = false;
    }

    public function render()
    {
        $results = collect();

        if ($this->hasSearched && !empty($this->searchQuery)) {
            $query = strtoupper($this->searchQuery);

            $results = Reservation::with('room')
                ->where(function ($q) use ($query) {
                    $q->where('protocol', $query)
                      ->orWhere('requester_email', strtolower($this->searchQuery));
                })
                ->orderBy('date', 'desc')
                ->get();
        }

        return view('livewire.public.reservation-status-check', [
            'results' => $results,
        ]);
    }
}
