<?php

use App\Livewire\Admin\ReservationEditor;
use App\Models\Reservation;
use App\Models\Room;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function () {
    $this->actingAs(User::factory()->create(['role' => 'super_admin']));
});

function createValidationTestRoom(): Room
{
    return Room::create([
        'name' => 'Sala de Testes',
        'code' => 'SALA-TESTE',
        'capacity' => 20,
        'is_active' => true,
    ]);
}

test('editor rejects past date', function () {
    $room = createValidationTestRoom();
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
});

test('editor rejects end time before start time', function () {
    $room = createValidationTestRoom();
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
});

test('editor accepts valid data', function () {
    $room = createValidationTestRoom();
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
});
