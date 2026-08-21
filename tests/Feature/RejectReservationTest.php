<?php

use App\Actions\RejectReservationAction;
use App\Models\Reservation;
use App\Models\Room;

function createRejectTestRoom(): Room
{
    return Room::create([
        'name' => 'Sala de Testes',
        'code' => 'SALA-TESTE',
        'capacity' => 20,
        'is_active' => true,
    ]);
}

test('can reject pending reservation', function () {
    $room = createRejectTestRoom();
    $reservation = Reservation::create([
        'room_id' => $room->id,
        'event_name' => 'Evento',
        'requester_name' => 'Teste',
        'requester_email' => 'teste@test.com',
        'date' => now()->addDays(5)->toDateString(),
        'start_time' => '09:00',
        'end_time' => '10:00',
        'attendees_count' => 10,
        'status' => 'pending',
    ]);

    $action = new RejectReservationAction;
    $result = $action->execute($reservation);

    expect($result->status->value)->toBe('rejected');
});

test('cannot reject already rejected reservation', function () {
    $room = createRejectTestRoom();
    $reservation = Reservation::create([
        'room_id' => $room->id,
        'event_name' => 'Evento',
        'requester_name' => 'Teste',
        'requester_email' => 'teste@test.com',
        'date' => now()->addDays(5)->toDateString(),
        'start_time' => '09:00',
        'end_time' => '10:00',
        'attendees_count' => 10,
        'status' => 'rejected',
    ]);

    $action = new RejectReservationAction;

    expect(fn () => $action->execute($reservation))
        ->toThrow(Exception::class, 'Esta reserva ja foi rejeitada anteriormente.');
});

test('can reject authorized reservation', function () {
    $room = createRejectTestRoom();
    $reservation = Reservation::create([
        'room_id' => $room->id,
        'event_name' => 'Evento',
        'requester_name' => 'Teste',
        'requester_email' => 'teste@test.com',
        'date' => now()->addDays(5)->toDateString(),
        'start_time' => '09:00',
        'end_time' => '10:00',
        'attendees_count' => 10,
        'status' => 'authorized',
    ]);

    $action = new RejectReservationAction;
    $result = $action->execute($reservation);

    expect($result->status->value)->toBe('rejected');
});
