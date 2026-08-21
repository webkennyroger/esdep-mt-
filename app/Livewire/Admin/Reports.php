<?php

namespace App\Livewire\Admin;

use App\Enums\ReservationStatus;
use App\Models\Reservation;
use App\Models\Room;
use Carbon\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Symfony\Component\HttpFoundation\StreamedResponse;

#[Layout('components.layouts.app')]
/** @property-read array<string, mixed> $stats */
class Reports extends Component
{
    public string $startDate = '';

    public string $endDate = '';

    public ?int $roomId = null;

    public function mount(): void
    {
        $this->startDate = Carbon::now()->startOfMonth()->format('Y-m-d');
        $this->endDate = Carbon::now()->endOfMonth()->format('Y-m-d');
    }

    public function getStatsProperty(): array
    {
        $query = Reservation::accessibleTo(auth()->user())
            ->whereBetween('date', [$this->startDate, $this->endDate]);

        if ($this->roomId) {
            $query->where('room_id', $this->roomId);
        }

        $total = (clone $query)->count();
        $approved = (clone $query)->where('status', ReservationStatus::AUTHORIZED)->count();
        $rejected = (clone $query)->where('status', ReservationStatus::REJECTED)->count();
        $pending = (clone $query)->where('status', ReservationStatus::PENDING)->count();
        $totalAttendees = (clone $query)->sum('attendees_count');

        $byRoom = Room::accessibleTo(auth()->user())
            ->when($this->roomId, fn ($rooms) => $rooms->whereKey($this->roomId))
            ->withCount(['reservations as total' => fn ($reservations) => $reservations
                ->whereBetween('date', [$this->startDate, $this->endDate])])
            ->get()
            ->map(fn (Room $room) => [
                'name' => $room->name,
                'total' => (int) $room->getAttribute('total'),
            ])
            ->filter(fn (array $item) => $item['total'] > 0)
            ->sortByDesc('total')
            ->toArray();

        $byService = [
            'coffee_break' => (clone $query)->where('coffee_break', true)->count(),
            'sonorizacao' => (clone $query)->where('sonorizacao', true)->count(),
            'transmissao' => (clone $query)->where('transmissao', true)->count(),
            'projetor' => (clone $query)->where('projetor', true)->count(),
        ];

        $occupancyRate = $total > 0 ? round(($approved / $total) * 100, 1) : 0;

        return compact(
            'total', 'approved', 'rejected', 'pending',
            'totalAttendees', 'byRoom', 'byService', 'occupancyRate'
        );
    }

    public function exportCsv(): StreamedResponse
    {
        $query = Reservation::accessibleTo(auth()->user())->with('room')
            ->whereBetween('date', [$this->startDate, $this->endDate])
            ->orderBy('date');

        if ($this->roomId) {
            $query->where('room_id', $this->roomId);
        }

        $reservations = $query->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="relatorio-reservas-'.$this->startDate.'-'.$this->endDate.'.csv"',
        ];

        return response()->stream(function () use ($reservations) {
            $handle = fopen('php://output', 'w');

            fputcsv($handle, ['Protocolo', 'Evento', 'Sala', 'Data', 'Horario', 'Solicitante', 'Email', 'Publico', 'Status', 'Coffee Break', 'Sonorizacao', 'Transmissao', 'Projetor'], ';');

            foreach ($reservations as $r) {
                fputcsv($handle, [
                    $r->protocol,
                    $r->event_name,
                    $r->room->name ?? '-',
                    $r->date->format('d/m/Y'),
                    "{$r->start_time} - {$r->end_time}",
                    $r->requester_name,
                    $r->requester_email,
                    $r->attendees_count,
                    $r->status->label(),
                    $r->coffee_break ? 'Sim' : 'Nao',
                    $r->sonorizacao ? 'Sim' : 'Nao',
                    $r->transmissao ? 'Sim' : 'Nao',
                    $r->projetor ? 'Sim' : 'Nao',
                ], ';');
            }

            fclose($handle);
        }, 200, $headers);
    }

    public function render()
    {
        return view('livewire.admin.reports', [
            'rooms' => Room::accessibleTo(auth()->user())->orderBy('name')->get(),
            'stats' => $this->getStatsProperty(),
        ]);
    }
}
