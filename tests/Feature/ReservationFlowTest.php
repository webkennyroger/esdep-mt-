<?php

use App\Enums\ReservationStatus;
use App\Livewire\Admin\AdminCalendar;
use App\Livewire\Admin\ReservationEditor;
use App\Livewire\Admin\ReservationManager;
use App\Livewire\Public\BookingForm;
use App\Models\Reservation;
use App\Models\Room;
use App\Models\User;
use Carbon\Carbon;
use Livewire\Livewire;

beforeEach(function () {
    $this->actingAs(User::factory()->create(['role' => 'super_admin']));
});

function createFlowTestRoom(): Room
{
    return Room::create([
        'name' => 'Sala Horizonte',
        'code' => 'HORIZONTE',
        'capacity' => 60,
        'theme_color' => '#008b5c',
        'description' => 'Sala de reuniões',
        'is_active' => true,
    ]);
}

test('public booking form saves all fields to database', function () {
    $room = createFlowTestRoom();
    $date = now()->addDays(2)->toDateString();

    Livewire::test(BookingForm::class)
        ->set('roomId', (string) $room->id)
        ->set('eventName', 'Seminário de Direito')
        ->set('description', 'Evento institucional importante')
        ->set('requesterName', 'Carlos Souza')
        ->set('requesterEmail', 'carlos@esdep.mt.gov.br')
        ->set('date', $date)
        ->set('startTime', '14:00')
        ->set('endTime', '16:00')
        ->set('attendeesCount', 40)
        ->set('coffeeBreak', true)
        ->set('sonorizacao', true)
        ->set('transmissao', true)
        ->set('termsAccepted', true)
        ->call('submit')
        ->assertHasNoErrors()
        ->assertDispatched('reservationCreated');

    $saved = Reservation::first();
    expect($saved)->not->toBeNull();
    expect($saved->room_id)->toBe($room->id);
    expect($saved->event_name)->toBe('Seminário de Direito');
    expect($saved->description)->toBe('Evento institucional importante');
    expect($saved->requester_name)->toBe('Carlos Souza');
    expect($saved->requester_email)->toBe('carlos@esdep.mt.gov.br');
    expect(Carbon::parse($saved->date)->toDateString())->toBe($date);
    expect(Carbon::parse($saved->start_time)->format('H:i'))->toBe('14:00');
    expect(Carbon::parse($saved->end_time)->format('H:i'))->toBe('16:00');
    expect($saved->attendees_count)->toBe(40);
    expect($saved->coffee_break)->toBeTrue();
    expect($saved->sonorizacao)->toBeTrue();
    expect($saved->transmissao)->toBeTrue();
    expect($saved->status)->toBe(ReservationStatus::PENDING);
});

test('admin reservation editor updates all fields', function () {
    $room = createFlowTestRoom();
    $date = now()->addDays(3)->toDateString();

    $reservation = Reservation::create([
        'room_id' => $room->id,
        'event_name' => 'Evento Antigo',
        'description' => 'Desc Antiga',
        'requester_name' => 'Ana',
        'requester_email' => 'ana@esdep.mt.gov.br',
        'date' => $date,
        'start_time' => '10:00',
        'end_time' => '11:00',
        'attendees_count' => 10,
        'coffee_break' => false,
        'sonorizacao' => false,
        'transmissao' => false,
        'status' => 'pending',
    ]);

    Livewire::test(ReservationEditor::class)
        ->call('open', $reservation->id)
        ->assertSet('eventName', 'Evento Antigo')
        ->assertSet('coffeeBreak', false)
        ->set('eventName', 'Evento Atualizado')
        ->set('coffeeBreak', true)
        ->set('sonorizacao', true)
        ->set('transmissao', true)
        ->set('attendeesCount', 25)
        ->call('save')
        ->assertHasNoErrors()
        ->assertSet('showModal', false);

    $fresh = $reservation->fresh();
    expect($fresh->event_name)->toBe('Evento Atualizado');
    expect($fresh->coffee_break)->toBeTrue();
    expect($fresh->sonorizacao)->toBeTrue();
    expect($fresh->transmissao)->toBeTrue();
    expect($fresh->attendees_count)->toBe(25);
});

test('admin approves and reservation appears in calendar', function () {
    $room = createFlowTestRoom();
    $date = now()->addDays(5)->toDateString();

    $reservation = Reservation::create([
        'room_id' => $room->id,
        'event_name' => 'Reunião de Diretoria',
        'description' => 'Pauta estratégica',
        'requester_name' => 'Diretor Roberto',
        'requester_email' => 'roberto@esdep.mt.gov.br',
        'date' => $date,
        'start_time' => '09:00',
        'end_time' => '11:00',
        'attendees_count' => 15,
        'coffee_break' => true,
        'sonorizacao' => true,
        'transmissao' => false,
        'status' => 'pending',
    ]);

    // Approve reservation
    Livewire::test(ReservationManager::class)
        ->call('openApproveModal', $reservation->id, $reservation->event_name)
        ->call('confirmApprove')
        ->assertDispatched('reservationApproved');

    $this->assertDatabaseHas('reservations', [
        'id' => $reservation->id,
        'status' => 'authorized',
    ]);

    // Check in AdminCalendar
    $calendar = Livewire::test(AdminCalendar::class);
    $calendar->call('loadReservations');

    $reservations = $calendar->get('reservations');
    expect($reservations)->toHaveKey($date);
    expect($reservations[$date][0]['event_name'])->toBe('Reunião de Diretoria');
    expect($reservations[$date][0]['coffee_break'])->toBeTrue();
    expect($reservations[$date][0]['sonorizacao'])->toBeTrue();
    expect($reservations[$date][0]['transmissao'])->toBeFalse();
});
