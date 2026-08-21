<?php

namespace App\Livewire\Public;

use App\Models\Reservation;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.public', ['title' => 'Consultar Reserva — ESDEP-MT', 'hideHeaderMenu' => true])]
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

        if ($this->hasSearched && ! empty($this->searchQuery)) {
            $search = $this->searchQuery;
            $upper = strtoupper($search);
            $lower = strtolower($search);

            $results = Reservation::with('room')
                ->where(function ($q) use ($search, $upper, $lower) {
                    $q->where('protocol', $upper)
                        ->orWhere('event_name', 'like', "%{$search}%")
                        ->orWhere('requester_name', 'like', "%{$search}%")
                        ->orWhere('requester_email', 'like', "%{$lower}%")
                        ->orWhere('start_time', 'like', "%{$search}%")
                        ->orWhere('end_time', 'like', "%{$search}%")
                        ->orWhere('date', 'like', "%{$search}%")
                        ->orWhereHas('room', function ($rq) use ($search) {
                            $rq->where('name', 'like', "%{$search}%")
                                ->orWhere('code', 'like', "%{$search}%");
                        });
                })
                ->orderBy('date', 'desc')
                ->get();
        }

        return view('livewire.public.reservation-status-check', [
            'results' => $results,
        ]);
    }
}
