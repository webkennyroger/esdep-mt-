<?php

namespace App\Services;

use App\Models\Reservation;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Collection;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ReservationExportService
{
    public const HEADERS = [
        'Protocolo', 'Evento', 'Sala', 'Data', 'Horário', 'Solicitante', 'Email',
        'Público', 'Status', 'Coffee Break', 'Sonorização', 'Transmissão', 'Projetor',
    ];

    /**
     * @return array<int, array<int, string|int>>
     */
    public function rows(Collection $reservations): array
    {
        return $reservations->map(fn (Reservation $r) => [
            $r->protocol,
            $r->event_name,
            $r->room->name ?? '-',
            $r->date->format('d/m/Y'),
            $r->formattedStartTime().' - '.$r->formattedEndTime(),
            $r->requester_name,
            $r->requester_email,
            $r->attendees_count,
            $r->status->label(),
            $r->coffee_break ? 'Sim' : 'Não',
            $r->sonorizacao ? 'Sim' : 'Não',
            $r->transmissao ? 'Sim' : 'Não',
            $r->projetor ? 'Sim' : 'Não',
        ])->all();
    }

    public function toXlsx(Collection $reservations): string
    {
        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->fromArray(self::HEADERS, null, 'A1');
        $sheet->fromArray($this->rows($reservations), null, 'A2');

        $writer = new Xlsx($spreadsheet);

        ob_start();
        $writer->save('php://output');

        return ob_get_clean();
    }

    public function toPdf(Collection $reservations): string
    {
        return Pdf::loadView('exports.reservations-pdf', [
            'headers' => self::HEADERS,
            'rows' => $this->rows($reservations),
        ])->output();
    }
}
