<?php

use App\Actions\ApproveReservationAction;
use App\Actions\CreateReservationAction;
use App\Models\Room;

function createConflictTestRoom(): Room
{
    return Room::create([
        'name' => 'Sala de Testes',
        'code' => 'SALA-TESTE',
        'capacity' => 20,
        'is_active' => true,
    ]);
}

test('cannot create reservation overlapping authorized', function () {
    $room = createConflictTestRoom();

    $createAction = new CreateReservationAction();
    $reservation1 = $createAction->execute([
        'room_id' => $room->id,
        'event_name' => 'Reunião 1',
        'requester_name' => 'João Silva',
        'requester_email' => 'joao@test.com',
        'date' => now()->addDay()->toDateString(),
        'start_time' => '09:00',
        'end_time' => '10:00',
        'attendees_count' => 10,
    ]);

    $approveAction = new ApproveReservationAction();
    $approveAction->execute($reservation1);

    expect(fn () => $createAction->execute([
        'room_id' => $room->id,
        'event_name' => 'Reunião 2',
        'requester_name' => 'Maria Santos',
        'requester_email' => 'maria@test.com',
        'date' => now()->addDay()->toDateString(),
        'start_time' => '09:30',
        'end_time' => '10:30',
        'attendees_count' => 5,
    ]))->toThrow(\Exception::class);
});

test('can create reservation after authorized ends', function () {
    $room = createConflictTestRoom();

    $createAction = new CreateReservationAction();
    $reservation1 = $createAction->execute([
        'room_id' => $room->id,
        'event_name' => 'Reunião 1',
        'requester_name' => 'João Silva',
        'requester_email' => 'joao@test.com',
        'date' => now()->addDay()->toDateString(),
        'start_time' => '09:00',
        'end_time' => '10:00',
        'attendees_count' => 10,
    ]);

    $approveAction = new ApproveReservationAction();
    $approveAction->execute($reservation1);

    $reservation2 = $createAction->execute([
        'room_id' => $room->id,
        'event_name' => 'Reunião 2',
        'requester_name' => 'Maria Santos',
        'requester_email' => 'maria@test.com',
        'date' => now()->addDay()->toDateString(),
        'start_time' => '10:00',
        'end_time' => '11:00',
        'attendees_count' => 5,
    ]);

    $this->assertDatabaseHas('reservations', ['event_name' => 'Reunião 2']);
});

test('cannot create pending overlapping pending', function () {
    $room = createConflictTestRoom();

    $action = new CreateReservationAction();

    $action->execute([
        'room_id' => $room->id,
        'event_name' => 'Reunião 1',
        'requester_name' => 'João Silva',
        'requester_email' => 'joao@test.com',
        'date' => now()->addDay()->toDateString(),
        'start_time' => '09:00',
        'end_time' => '10:00',
        'attendees_count' => 10,
    ]);

    expect(fn () => $action->execute([
        'room_id' => $room->id,
        'event_name' => 'Reunião 2',
        'requester_name' => 'Maria Santos',
        'requester_email' => 'maria@test.com',
        'date' => now()->addDay()->toDateString(),
        'start_time' => '09:30',
        'end_time' => '10:30',
        'attendees_count' => 5,
    ]))->toThrow(\Exception::class);
});
