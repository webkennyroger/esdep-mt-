<?php

namespace App\Services;

use App\Models\Reservation;
use App\Models\ContactGroup;
use Google\Client;
use Google\Service\Calendar;
use Google\Service\Calendar\ConferenceData;
use Google\Service\Calendar\CreateConferenceRequest;
use Google\Service\Calendar\Event;
use Google\Service\Calendar\EventAttendee;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;

class GoogleCalendarService
{
    public function sync(Reservation $reservation): void
    {
        if (! config('services.google_calendar.enabled')) {
            return;
        }

        try {
            $calendar = new Calendar($this->client());
            $event = $this->event($reservation, $calendar);
            $calendarId = config('services.google_calendar.calendar_id', 'primary');

            if ($reservation->google_event_id) {
                $event = $calendar->events->update($calendarId, $reservation->google_event_id, $event, [
                    'sendUpdates' => 'all',
                    'conferenceDataVersion' => 1,
                ]);
            } else {
                $event = $calendar->events->insert($calendarId, $event, [
                    'sendUpdates' => 'all',
                    'conferenceDataVersion' => 1,
                ]);
            }

            $reservation->update([
                'google_event_id' => $event->getId(),
                'google_meet_url' => $event->getHangoutLink(),
                'google_sync_status' => 'synced',
                'google_sync_error' => null,
                'google_synced_at' => now(),
            ]);
        } catch (\Throwable $exception) {
            $reservation->update([
                'google_sync_status' => 'failed',
                'google_sync_error' => Str::limit($exception->getMessage(), 2000),
            ]);
            report($exception);
        }
    }

    private function client(): Client
    {
        $client = new Client;
        $client->setApplicationName(config('app.name'));
        $client->setScopes([Calendar::CALENDAR_EVENTS]);

        $credentialsPath = config('services.google_calendar.credentials_path');
        $credentialsJson = config('services.google_calendar.credentials_json');
        if ($credentialsJson) {
            $client->setAuthConfig(json_decode($credentialsJson, true, 512, JSON_THROW_ON_ERROR));
        } elseif ($credentialsPath) {
            $client->setAuthConfig($credentialsPath);
        } else {
            throw new \RuntimeException('Credenciais do Google Calendar não configuradas.');
        }

        if ($impersonate = config('services.google_calendar.impersonate')) {
            $client->setSubject($impersonate);
        }

        return $client;
    }

    private function event(Reservation $reservation, Calendar $calendar): Event
    {
        $start = $this->dateTime($reservation->date, $reservation->start_time);
        $end = $this->dateTime($reservation->date, $reservation->end_time);
        $group = $reservation->room?->contactGroup;
        $responsibles = $group instanceof ContactGroup ? $group->responsibleContacts : new Collection;
        $attendees = $responsibles
            ->map(fn ($contact) => new EventAttendee(['email' => $contact->email, 'displayName' => $contact->name]))
            ->all();

        $event = new Event([
            'summary' => $reservation->event_name,
            'description' => strip_tags($reservation->description ?? '')."\n\nProtocolo: {$reservation->protocol}",
            'location' => $reservation->room->name ?? 'ESDEP-MT',
            'start' => ['dateTime' => $start, 'timeZone' => config('services.google_calendar.timezone')],
            'end' => ['dateTime' => $end, 'timeZone' => config('services.google_calendar.timezone')],
            'attendees' => $attendees,
        ]);

        $existing = $reservation->google_event_id
            ? $calendar->events->get(config('services.google_calendar.calendar_id', 'primary'), $reservation->google_event_id)
            : null;

        if ($reservation->transmissao && ! $existing?->getConferenceData()) {
            $conference = new ConferenceData;
            $conference->setCreateRequest(new CreateConferenceRequest([
                'requestId' => 'reserva-'.$reservation->id.'-'.Str::uuid(),
                'conferenceSolutionKey' => ['type' => 'hangoutsMeet'],
            ]));
            $event->setConferenceData($conference);
        }

        if ($existing?->getConferenceData()) {
            $event->setConferenceData($existing->getConferenceData());
        }

        return $event;
    }

    private function dateTime(mixed $date, mixed $time): string
    {
        return sprintf('%sT%s:00', $date instanceof \DateTimeInterface ? $date->format('Y-m-d') : $date, substr((string) $time, 0, 5));
    }
}