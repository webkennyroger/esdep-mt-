<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Room;
use App\Models\Reservation;
use App\Actions\RejectReservationAction;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RejectReservationTest extends TestCase
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

    public function test_can_reject_pending_reservation(): void
    {
        $room = $this->createRoom();
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

        $action = new RejectReservationAction();
        $result = $action->execute($reservation);

        $this->assertEquals('rejected', $result->status->value);
    }

    public function test_cannot_reject_already_rejected_reservation(): void
    {
        $room = $this->createRoom();
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

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Esta reserva ja foi rejeitada anteriormente.');

        $action = new RejectReservationAction();
        $action->execute($reservation);
    }

    public function test_can_reject_authorized_reservation(): void
    {
        $room = $this->createRoom();
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

        $action = new RejectReservationAction();
        $result = $action->execute($reservation);

        $this->assertEquals('rejected', $result->status->value);
    }
}
