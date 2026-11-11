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
    private function applyDateRange($query, ?string $start = null, ?string $end = null)
    {
        return $query->whereDate('date', '>=', $start ?? $this->startDate)->whereDate('date', '<=', $end ?? $this->endDate);
    }

    /**
     * Variacao percentual contra o periodo anterior (mesma quantidade de dias,
     * imediatamente antes do inicio do periodo atual) - mesma regra do Reports do admin.
     *
     * @return array{total: int, approved: int, rejected: int, totalAttendees: int}
     */
    private function trends($ids, int $total, int $approved, int $rejected, $totalAttendees): array
    {
        $days = Carbon::parse($this->startDate)->diffInDays(Carbon::parse($this->endDate)) + 1;
        $prevEnd = Carbon::parse($this->startDate)->subDay();
        $prevStart = $prevEnd->copy()->subDays($days - 1);

        $prev = $this->applyDateRange(Reservation::whereIn('room_id', $ids), $prevStart->format('Y-m-d'), $prevEnd->format('Y-m-d'))
            ->when($this->roomId, fn ($q) => $q->where('room_id', $this->roomId));

        $pct = fn (int|float $current, int|float $previous): int => $previous > 0
            ? (int) round((($current - $previous) / $previous) * 100)
            : ($current > 0 ? 100 : 0);

        return [
            'total' => $pct($total, (clone $prev)->count()),
            'approved' => $pct($approved, (clone $prev)->where('status', ReservationStatus::AUTHORIZED)->count()),
            'rejected' => $pct($rejected, (clone $prev)->where('status', ReservationStatus::REJECTED)->count()),
            'totalAttendees' => $pct($totalAttendees, (clone $prev)->sum('attendees_count')),
        ];
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
        $totalAttendees = (clone $query)->sum('attendees_count');
        $stats = ['total' => $total, 'approved' => $approved, 'rejected' => $rejected, 'pending' => $pending, 'totalAttendees' => $totalAttendees,
            'trends' => $this->trends($ids, $total, $approved, $rejected, $totalAttendees),
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
