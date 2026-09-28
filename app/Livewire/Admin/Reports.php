<?php

namespace App\Livewire\Admin;

use App\Enums\ReservationStatus;
use App\Models\Reservation;
use App\Models\Room;
use App\Services\ReservationExportService;
use Carbon\Carbon;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;
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

        $statusBreakdown = [
            'approved' => ['label' => 'Autorizada', 'value' => $approved, 'pct' => $total > 0 ? round(($approved / $total) * 100, 1) : 0],
            'pending' => ['label' => 'Pendente', 'value' => $pending, 'pct' => $total > 0 ? round(($pending / $total) * 100, 1) : 0],
            'rejected' => ['label' => 'Rejeitada', 'value' => $rejected, 'pct' => $total > 0 ? round(($rejected / $total) * 100, 1) : 0],
        ];

        return compact(
            'total', 'approved', 'rejected', 'pending',
            'totalAttendees', 'byRoom', 'byService', 'occupancyRate', 'statusBreakdown'
        );
    }

    private function reservationsForExport(): Collection
    {
        $query = Reservation::accessibleTo(auth()->user())->with('room')
            ->whereBetween('date', [$this->startDate, $this->endDate])
            ->orderBy('date');

        if ($this->roomId) {
            $query->where('room_id', $this->roomId);
        }

        return $query->get();
    }

    public function exportExcel(): Response
    {
        $bytes = app(ReservationExportService::class)->toXlsx($this->reservationsForExport());

        return response($bytes, 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="relatorio-reservas-'.$this->startDate.'-'.$this->endDate.'.xlsx"',
        ]);
    }

    public function exportPdf(): Response
    {
        $bytes = app(ReservationExportService::class)->toPdf($this->reservationsForExport());

        return response($bytes, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="relatorio-reservas-'.$this->startDate.'-'.$this->endDate.'.pdf"',
        ]);
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
