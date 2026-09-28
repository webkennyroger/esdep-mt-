<?php

use App\Enums\ReservationStatus;
use App\Livewire\Admin\ReservationEditor;
use App\Mail\ReservationRequestedMail;
use App\Models\Reservation;
use App\Models\Room;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;

beforeEach(function () {
    $this->actingAs(User::factory()->superAdmin()->create());
    $this->room = Room::create([
        'name' => 'Sala Horizonte',
        'code' => 'HORIZONTE',
        'capacity' => 60,
        'theme_color' => '#008b5c',
        'is_active' => true,
    ]);
});

test('opening the creator resets the form and marks it as a new reservation', function () {
    Livewire::test(ReservationEditor::class)
        ->call('openCreate')
        ->assertSet('reservationId', 0)
        ->assertSet('eventName', '')
        ->assertSet('attendeesCount', 1)
        ->assertSet('showModal', true);
});

test('admin creates a reservation that is already authorized and sends no notification email', function () {
    Mail::fake();

    Livewire::test(ReservationEditor::class)
        ->call('openCreate')
        ->set('eventName', 'Reunião Interna')
        ->set('roomId', $this->room->id)
        ->set('date', now()->addDays(5)->toDateString())
        ->set('startTime', '09:00')
        ->set('endTime', '10:00')
        ->set('requesterName', 'Fulano de Tal')
        ->set('requesterEmail', 'fulano@esdep.mt.gov.br')
        ->set('attendeesCount', 10)
        ->call('save')
        ->assertDispatched('reservationCreated');

    $reservation = Reservation::where('event_name', 'Reunião Interna')->first();

    expect($reservation)->not->toBeNull()
        ->and($reservation->status)->toBe(ReservationStatus::AUTHORIZED)
        ->and($reservation->protocol)->not->toBeNull();

    Mail::assertNotSent(ReservationRequestedMail::class);
});
