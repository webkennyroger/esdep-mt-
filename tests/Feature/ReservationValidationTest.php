<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Room;
use App\Models\Reservation;
use Livewire\Livewire;
use App\Livewire\Admin\ReservationEditor;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ReservationValidationTest extends TestCase
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

    public function test_editor_rejects_past_date(): void
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

        Livewire::test(ReservationEditor::class)
            ->call('open', $reservation->id)
            ->set('date', now()->subDay()->format('Y-m-d'))
            ->call('save')
            ->assertHasErrors(['date']);
    }

    public function test_editor_rejects_end_time_before_start_time(): void
    {
        $room = $this->createRoom();
        $reservation = Reservation::create([
            'room_id' => $room->id,
            'event_name' => 'Evento',
            'requester_name' => 'Teste',
            'requester_email' => 'teste@test.com',
            'date' => now()->addDays(5)->toDateString(),
            'start_time' => '14:00',
            'end_time' => '15:00',
            'attendees_count' => 10,
            'status' => 'pending',
        ]);

        Livewire::test(ReservationEditor::class)
            ->call('open', $reservation->id)
            ->set('endTime', '13:00')
            ->call('save')
            ->assertHasErrors(['endTime']);
    }

    public function test_editor_accepts_valid_data(): void
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

        Livewire::test(ReservationEditor::class)
            ->call('open', $reservation->id)
            ->set('date', now()->addDays(10)->format('Y-m-d'))
            ->set('startTime', '14:00')
            ->set('endTime', '16:00')
            ->call('save')
            ->assertHasNoErrors()
            ->assertSet('showModal', false);
    }
}
