<?php

namespace App\Services;

use App\Models\Reservation;
use Carbon\Carbon;

class IcsCalendarGenerator
{
    public function generate(Reservation $reservation): string
    {
        $dtStart = Carbon::parse($reservation->date)->setTimeFromTimeString($reservation->start_time);
        $dtEnd = Carbon::parse($reservation->date)->setTimeFromTimeString($reservation->end_time);

        $lines = [
            'BEGIN:VCALENDAR',
            'VERSION:2.0',
            'PRODID:-//ESDEP-MT//Reservas//PT',
            'CALSCALE:GREGORIAN',
            'METHOD:PUBLISH',
            'BEGIN:VEVENT',
            'UID:'.$reservation->protocol.'@esdep-mt',
            'DTSTART:'.$dtStart->format('Ymd\THis'),
            'DTEND:'.$dtEnd->format('Ymd\THis'),
            'SUMMARY:'.$this->escapeText($reservation->event_name),
            'DESCRIPTION:'.$this->escapeText($this->buildDescription($reservation)),
            'LOCATION:'.$this->escapeText($reservation->room->name ?? 'ESDEP-MT'),
            'STATUS:CONFIRMED',
            'DTSTAMP:'.Carbon::now()->format('Ymd\THis'),
            'END:VEVENT',
            'END:VCALENDAR',
        ];

        return implode("\r\n", $lines);
    }

    public function getFilename(Reservation $reservation): string
    {
        $safeName = preg_replace('/[^a-zA-Z0-9]/', '-', $reservation->event_name);
        $safeName = preg_replace('/-+/', '-', $safeName);
        $safeName = trim($safeName, '-');

        return "reserva-{$reservation->protocol}-{$safeName}.ics";
    }

    /**
     * Link de "adicionar evento" do Google Agenda, sem precisar de OAuth —
     * so abre a tela de criacao de evento do Google ja preenchida.
     */
    public function googleCalendarUrl(Reservation $reservation): string
    {
        $dtStart = Carbon::parse($reservation->date)->setTimeFromTimeString($reservation->start_time);
        $dtEnd = Carbon::parse($reservation->date)->setTimeFromTimeString($reservation->end_time);

        $params = [
            'action' => 'TEMPLATE',
            'text' => $reservation->event_name,
            'dates' => $dtStart->format('Ymd\THis').'/'.$dtEnd->format('Ymd\THis'),
            'details' => str_replace('\\n', "\n", $this->buildDescription($reservation)),
            'location' => $reservation->room->name ?? 'ESDEP-MT',
            'ctz' => config('app.timezone'),
        ];

        return 'https://calendar.google.com/calendar/render?'.http_build_query($params);
    }

    private function buildDescription(Reservation $reservation): string
    {
        $parts = [];

        if ($reservation->description) {
            $parts[] = strip_tags($reservation->description);
        }

        $parts[] = "Protocolo: {$reservation->protocol}";
        $parts[] = "Solicitante: {$reservation->requester_name} ({$reservation->requester_email})";
        $parts[] = 'Horário: '.$reservation->formattedStartTime().' - '.$reservation->formattedEndTime();

        $services = [];
        if ($reservation->coffee_break) {
            $services[] = 'Coffee Break';
        }
        if ($reservation->sonorizacao) {
            $services[] = 'Sonorização';
        }
        if ($reservation->transmissao) {
            $services[] = 'Transmissão';
        }
        if ($reservation->projetor) {
            $services[] = 'Projetor';
        }

        if ($services) {
            $parts[] = 'Serviços: '.implode(', ', $services);
        }

        return implode('\\n', $parts);
    }

    private function escapeText(string $text): string
    {
        $text = str_replace('\\', '\\\\', $text);
        $text = str_replace(';', '\\;', $text);
        $text = str_replace(',', '\\,', $text);
        $text = str_replace("\n", '\\n', $text);

        return $text;
    }
}
