<?php

namespace App\Livewire\Contact;

use App\Enums\ReservationStatus;
use App\Models\Reservation;
use App\Models\Room;
use Carbon\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Symfony\Component\HttpFoundation\StreamedResponse;

#[Layout('components.layouts.contact', ['title' => 'Relatórios — ESDEP-MT'])]
class ContactReports extends Component
{
    public string $startDate = '';

    public string $endDate = '';

    public ?int $roomId = null;

    public function mount(): void
    {
        $this->startDate = Carbon::now()->startOfMonth()->format('Y-m-d');
        $this->endDate = Carbon::now()->endOfMonth()->format('Y-m-d');
    }

    public function exportCsv(): StreamedResponse
    {
        $roomIds = auth('contact')->user()->managedRoomIds();
        $rows = Reservation::with('room')->whereIn('room_id', $roomIds)
            ->whereBetween('date', [$this->startDate, $this->endDate])
            ->when($this->roomId, fn ($query) => $query->where('room_id', $this->roomId))
            ->orderBy('date')->get();

        return response()->streamDownload(function () use ($rows): void {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Protocolo', 'Evento', 'Sala', 'Data', 'Horário', 'Solicitante', 'Público', 'Status'], ';');
            foreach ($rows as $reservation) {
                fputcsv($handle, [$reservation->protocol, $reservation->event_name, $reservation->room->name, $reservation->date->format('d/m/Y'), $reservation->formattedStartTime().' - '.$reservation->formattedEndTime(), $reservation->requester_name, $reservation->attendees_count, $reservation->status->label()], ';');
            }
            fclose($handle);
        }, 'relatorio-reservas-'.$this->startDate.'-'.$this->endDate.'.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    public function render()
    {
        $ids = auth('contact')->user()->managedRoomIds();
        $query = Reservation::whereIn('room_id', $ids)->whereBetween('date', [$this->startDate, $this->endDate])->when($this->roomId, fn ($q) => $q->where('room_id', $this->roomId));
        $total = (clone $query)->count();
        $approved = (clone $query)->where('status', ReservationStatus::AUTHORIZED)->count();
        $stats = ['total' => $total, 'approved' => $approved, 'rejected' => (clone $query)->where('status', ReservationStatus::REJECTED)->count(), 'pending' => (clone $query)->where('status', ReservationStatus::PENDING)->count(), 'totalAttendees' => (clone $query)->sum('attendees_count'),
            'byRoom' => Room::whereIn('id', $ids)->withCount(['reservations as total' => fn ($q) => $q->whereBetween('date', [$this->startDate, $this->endDate])])->get()->map(fn ($r) => ['name' => $r->name, 'total' => (int) $r->getAttribute('total')])->filter(fn ($r) => $r['total'] > 0)->toArray(),
            'byService' => ['coffee_break' => (clone $query)->where('coffee_break', true)->count(), 'sonorizacao' => (clone $query)->where('sonorizacao', true)->count(), 'transmissao' => (clone $query)->where('transmissao', true)->count(), 'projetor' => (clone $query)->where('projetor', true)->count()],
            'occupancyRate' => $total ? round($approved / $total * 100, 1) : 0];

        return view('livewire.admin.reports', ['rooms' => Room::whereIn('id', $ids)->orderBy('name')->get(), 'stats' => $stats]);
    }
}
