<?php

namespace App\Livewire\Admin;

use App\Enums\ReservationStatus;
use App\Models\Reservation;
use App\Models\Room;
use App\Services\ReservationExportService;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
/** @property-read array<string, mixed> $stats */
class Reports extends Component
{
    public string $startDate = '';

    public string $endDate = '';

    public ?int $roomId = null;

    public function mount(): void
    {
        abort_unless(auth()->user()?->can('access-reports'), 403);

        $this->startDate = Carbon::now()->startOfMonth()->format('Y-m-d');
        $this->endDate = Carbon::now()->endOfMonth()->format('Y-m-d');
    }

    public function getStatsProperty(): array
    {
        $query = Reservation::accessibleTo(auth()->user())
            ->whereDate('date', '>=', $this->startDate)
            ->whereDate('date', '<=', $this->endDate);

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
                ->whereDate('date', '>=', $this->startDate)
                ->whereDate('date', '<=', $this->endDate)])
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

        $statusBreakdown = [
            'approved' => ['label' => 'Autorizada', 'value' => $approved, 'pct' => $total > 0 ? round(($approved / $total) * 100, 1) : 0],
            'pending' => ['label' => 'Pendente', 'value' => $pending, 'pct' => $total > 0 ? round(($pending / $total) * 100, 1) : 0],
            'rejected' => ['label' => 'Rejeitada', 'value' => $rejected, 'pct' => $total > 0 ? round(($rejected / $total) * 100, 1) : 0],
        ];

        // Periodo anterior: mesma quantidade de dias, imediatamente antes do inicio do periodo atual.
        $days = Carbon::parse($this->startDate)->diffInDays(Carbon::parse($this->endDate)) + 1;
        $prevEnd = Carbon::parse($this->startDate)->subDay();
        $prevStart = $prevEnd->copy()->subDays($days - 1);

        $prevQuery = Reservation::accessibleTo(auth()->user())
            ->whereDate('date', '>=', $prevStart->format('Y-m-d'))
            ->whereDate('date', '<=', $prevEnd->format('Y-m-d'));

        if ($this->roomId) {
            $prevQuery->where('room_id', $this->roomId);
        }

        $prevTotal = (clone $prevQuery)->count();
        $prevApproved = (clone $prevQuery)->where('status', ReservationStatus::AUTHORIZED)->count();
        $prevRejected = (clone $prevQuery)->where('status', ReservationStatus::REJECTED)->count();
        $prevTotalAttendees = (clone $prevQuery)->sum('attendees_count');

        $pct = fn (int|float $current, int|float $previous): int => $previous > 0
            ? (int) round((($current - $previous) / $previous) * 100)
            : ($current > 0 ? 100 : 0);

        $trends = [
            'total' => $pct($total, $prevTotal),
            'approved' => $pct($approved, $prevApproved),
            'rejected' => $pct($rejected, $prevRejected),
            'totalAttendees' => $pct($totalAttendees, $prevTotalAttendees),
        ];

        return compact(
            'total', 'approved', 'rejected', 'pending',
            'totalAttendees', 'byRoom', 'byService', 'occupancyRate', 'statusBreakdown', 'trends'
        );
    }

    private function reservationsForExport(): Collection
    {
        $query = Reservation::accessibleTo(auth()->user())->with('room')
            ->whereDate('date', '>=', $this->startDate)
            ->whereDate('date', '<=', $this->endDate)
            ->orderBy('date');

        if ($this->roomId) {
            $query->where('room_id', $this->roomId);
        }

        return $query->get();
    }

    public function exportExcel(): StreamedResponse
    {
        $bytes = app(ReservationExportService::class)->toXlsx($this->reservationsForExport());

        // O Livewire so intercepta o download (pra nao tentar colocar bytes
        // binarios dentro do payload JSON da acao) quando o retorno e um
        // StreamedResponse/BinaryFileResponse - um Response comum quebra com
        // "Malformed UTF-8 characters".
        return response()->streamDownload(
            fn () => print($bytes),
            'relatorio-reservas-'.$this->startDate.'-'.$this->endDate.'.xlsx',
            ['Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']
        );
    }

    public function exportPdf(): StreamedResponse
    {
        $bytes = app(ReservationExportService::class)->toPdf($this->reservationsForExport());

        return response()->streamDownload(
            fn () => print($bytes),
            'relatorio-reservas-'.$this->startDate.'-'.$this->endDate.'.pdf',
            ['Content-Type' => 'application/pdf']
        );
    }

    public function copyText(): string
    {
        $service = app(ReservationExportService::class);

        $lines = [implode("\t", ReservationExportService::HEADERS)];
        foreach ($service->rows($this->reservationsForExport()) as $row) {
            $lines[] = implode("\t", $row);
        }

        return implode("\n", $lines);
    }

    public function printHtml(): string
    {
        $service = app(ReservationExportService::class);

        return view('exports.print-table', [
            'headers' => ReservationExportService::HEADERS,
            'rows' => $service->rows($this->reservationsForExport()),
        ])->render();
    }

    public function render()
    {
        return view('livewire.admin.reports', [
            'rooms' => Room::accessibleTo(auth()->user())->orderBy('name')->get(),
            'stats' => $this->getStatsProperty(),
        ]);
    }
}
