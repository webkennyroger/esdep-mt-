<?php

use App\Enums\ReservationStatus;
use App\Livewire\Admin\AdminCalendar;
use App\Models\Reservation;
use App\Models\Room;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function () {
    $this->actingAs(User::factory()->superAdmin()->create());
});

test('month grid shows both start and end time for a reservation', function () {
    $room = Room::create(['name' => 'Sala Agenda', 'code' => 'AGE-01', 'capacity' => 10, 'is_active' => true]);
    Reservation::create([
        'room_id' => $room->id,
        'event_name' => 'Evento Teste',
        'requester_name' => 'Solicitante',
        'requester_email' => 'solicitante@example.com',
        'date' => '2026-09-15',
        'start_time' => '09:00',
        'end_time' => '10:30',
        'attendees_count' => 5,
        'status' => ReservationStatus::AUTHORIZED,
    ]);

    Livewire::test(AdminCalendar::class)
        ->set('currentMonth', 9)
        ->set('currentYear', 2026)
        ->assertSee('09:00–10:30');
});

test('month name starts with an uppercase letter', function () {
    Livewire::test(AdminCalendar::class)
        ->set('currentMonth', 9)
        ->set('currentYear', 2026)
        ->assertSee('Setembro de 2026')
        ->assertDontSee('setembro de 2026');
});

test('selected date label capitalizes the month name', function () {
    Livewire::test(AdminCalendar::class)
        ->call('selectDate', '2026-09-02')
        ->assertSee('de Setembro de 2026')
        ->assertDontSee('de setembro de 2026');
});
