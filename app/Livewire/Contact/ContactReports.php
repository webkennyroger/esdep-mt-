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

    /**
     * whereBetween compara a string da data diretamente contra a coluna, que
     * guarda um datetime ("Y-m-d H:i:s"); no limite superior isso exclui
     * reservas datadas exatamente no ultimo dia do intervalo. whereDate
     * extrai so a parte da data antes de comparar.
     */
    private function applyDateRange($query)
    {
        return $query->whereDate('date', '>=', $this->startDate)->whereDate('date', '<=', $this->endDate);
    }

    public function exportCsv(): StreamedResponse
    {
        $roomIds = auth('contact')->user()->managedRoomIds();
        $rows = $this->applyDateRange(Reservation::with('room')->whereIn('room_id', $roomIds))
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
        $query = $this->applyDateRange(Reservation::whereIn('room_id', $ids))->when($this->roomId, fn ($q) => $q->where('room_id', $this->roomId));
        $total = (clone $query)->count();
        $approved = (clone $query)->where('status', ReservationStatus::AUTHORIZED)->count();
        $rejected = (clone $query)->where('status', ReservationStatus::REJECTED)->count();
        $pending = (clone $query)->where('status', ReservationStatus::PENDING)->count();
        $stats = ['total' => $total, 'approved' => $approved, 'rejected' => $rejected, 'pending' => $pending, 'totalAttendees' => (clone $query)->sum('attendees_count'),
            'byRoom' => Room::whereIn('id', $ids)->withCount(['reservations as total' => fn ($q) => $this->applyDateRange($q)])->get()->map(fn ($r) => ['name' => $r->name, 'total' => (int) $r->getAttribute('total')])->filter(fn ($r) => $r['total'] > 0)->toArray(),
            'byService' => ['coffee_break' => (clone $query)->where('coffee_break', true)->count(), 'sonorizacao' => (clone $query)->where('sonorizacao', true)->count(), 'transmissao' => (clone $query)->where('transmissao', true)->count(), 'projetor' => (clone $query)->where('projetor', true)->count()],
            'occupancyRate' => $total ? round($approved / $total * 100, 1) : 0,
            'statusBreakdown' => [
                'approved' => ['label' => 'Autorizada', 'value' => $approved, 'pct' => $total > 0 ? round(($approved / $total) * 100, 1) : 0],
                'pending' => ['label' => 'Pendente', 'value' => $pending, 'pct' => $total > 0 ? round(($pending / $total) * 100, 1) : 0],
                'rejected' => ['label' => 'Rejeitada', 'value' => $rejected, 'pct' => $total > 0 ? round(($rejected / $total) * 100, 1) : 0],
            ]];

        return view('livewire.admin.reports', ['rooms' => Room::whereIn('id', $ids)->orderBy('name')->get(), 'stats' => $stats]);
    }
}
