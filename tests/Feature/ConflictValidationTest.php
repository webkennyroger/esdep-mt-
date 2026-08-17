<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Room;
use App\Actions\CreateReservationAction;
use App\Actions\ApproveReservationAction;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ConflictValidationTest extends TestCase
{
    use RefreshDatabase;

    private function createRoom(): Room
    {
        return Room::create([
            'name' => 'Sala de Testes',
            'code' => 'SALA-TESTE',
            'capacity' => 20,
            'is_active' => true,
        ]);
    }

    public function test_cannot_create_reservation_overlapping_authorized(): void
    {
        $room = $this->createRoom();

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

        $this->expectException(\Exception::class);

        $createAction->execute([
            'room_id' => $room->id,
            'event_name' => 'Reunião 2',
            'requester_name' => 'Maria Santos',
            'requester_email' => 'maria@test.com',
            'date' => now()->addDay()->toDateString(),
            'start_time' => '09:30',
            'end_time' => '10:30',
            'attendees_count' => 5,
        ]);
    }

    public function test_can_create_reservation_after_authorized_ends(): void
    {
        $room = $this->createRoom();

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
    }

    public function test_cannot_create_pending_overlapping_pending(): void
    {
        $room = $this->createRoom();

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

        $this->expectException(\Exception::class);

        $action->execute([
            'room_id' => $room->id,
            'event_name' => 'Reunião 2',
            'requester_name' => 'Maria Santos',
            'requester_email' => 'maria@test.com',
            'date' => now()->addDay()->toDateString(),
            'start_time' => '09:30',
            'end_time' => '10:30',
            'attendees_count' => 5,
        ]);
    }
}
