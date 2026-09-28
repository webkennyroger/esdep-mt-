<?php

use App\Mail\EventAnnouncementMail;
use App\Mail\ReservationApprovedMail;
use App\Mail\ReservationRejectedMail;
use App\Models\Reservation;
use App\Models\Room;

function createEmailTestReservation(): Reservation
{
    $room = Room::create([
        'name' => 'Sala Aurora',
        'code' => 'AURORA',
        'capacity' => 40,
        'theme_color' => '#008b5c',
        'is_active' => true,
    ]);

    return Reservation::create([
        'room_id' => $room->id,
        'event_name' => 'Evento de Teste',
        'requester_name' => 'Solicitante Teste',
        'requester_email' => 'solicitante@example.com',
        'date' => '2026-09-02',
        'start_time' => '09:00',
        'end_time' => '10:00',
        'attendees_count' => 5,
        'status' => 'authorized',
    ]);
}

test('reservation approved email capitalizes the month name', function () {
    $reservation = createEmailTestReservation();

    $html = (new ReservationApprovedMail($reservation))->render();

    expect($html)->toContain('de Setembro de 2026')
        ->not->toContain('de setembro de 2026');
});

test('reservation rejected email capitalizes the month name', function () {
    $reservation = createEmailTestReservation();

    $html = (new ReservationRejectedMail($reservation))->render();

    expect($html)->toContain('de Setembro de 2026')
        ->not->toContain('de setembro de 2026');
});

test('event announcement email capitalizes the month name', function () {
    $reservation = createEmailTestReservation();

    $html = (new EventAnnouncementMail($reservation, 'Convidado Teste'))->render();

    expect($html)->toContain('de Setembro de 2026')
        ->not->toContain('de setembro de 2026');
});
