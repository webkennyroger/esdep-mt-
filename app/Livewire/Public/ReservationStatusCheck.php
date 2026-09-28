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

    /**
     * Interpreta a busca como data no formato dia[/mes[/ano]]. Retorna null
     * quando a busca nao tem cara de data (ou os numeros sao invalidos), para
     * nao acoplar o filtro de data a buscas por evento/sala/protocolo.
     *
     * @return array{day: int, month: int|null, year: int|null}|null
     */
    private function parseDateSearch(string $search): ?array
    {
        if (! preg_match('/^(\d{1,2})(?:\/(\d{1,2})(?:\/(\d{4}))?)?$/', trim($search), $matches)) {
            return null;
        }

        $day = (int) $matches[1];
        $month = isset($matches[2]) && $matches[2] !== '' ? (int) $matches[2] : null;
        $year = isset($matches[3]) && $matches[3] !== '' ? (int) $matches[3] : null;

        if ($day < 1 || $day > 31) {
            return null;
        }

        if ($month !== null && ($month < 1 || $month > 12)) {
            return null;
        }

        return ['day' => $day, 'month' => $month, 'year' => $year];
    }

    public function render()
    {
        $results = collect();

        if ($this->hasSearched && ! empty($this->searchQuery)) {
            $search = $this->searchQuery;
            $upper = strtoupper($search);
            $lowerTerm = '%'.strtolower($search).'%';
            $datePart = $this->parseDateSearch($search);

            $results = Reservation::with('room')
                ->where(function ($q) use ($upper, $lowerTerm, $datePart) {
                    $q->where('protocol', 'like', "%{$upper}%")
                        ->orWhereRaw('LOWER(event_name) LIKE ?', [$lowerTerm])
                        ->orWhereHas('room', function ($rq) use ($lowerTerm) {
                            $rq->whereRaw('LOWER(name) LIKE ?', [$lowerTerm]);
                        });

                    if ($datePart !== null) {
                        $q->orWhere(function ($dq) use ($datePart) {
                            $dq->whereDay('date', $datePart['day']);

                            if ($datePart['month'] !== null) {
                                $dq->whereMonth('date', $datePart['month']);
                            }

                            if ($datePart['year'] !== null) {
                                $dq->whereYear('date', $datePart['year']);
                            }
                        });
                    }
                })
                ->orderBy('date', 'desc')
                ->get();
        }

        return view('livewire.public.reservation-status-check', [
            'results' => $results,
        ]);
    }
}
