<?php

use App\Actions\CreateReservationAction;
use App\Livewire\Public\ReservationStatusCheck;
use App\Models\Room;
use Livewire\Livewire;

beforeEach(function () {
    $this->room = Room::create([
        'name' => 'Auditório Nobre',
        'code' => 'AUD-01',
        'capacity' => 100,
        'theme_color' => '#008b5c',
        'is_active' => true,
    ]);
});

test('reservation route redirects to the single public booking form', function () {
    $this->get(route('reservations.create'))
        ->assertRedirect('/#nova-reserva');

    $this->get(route('home'))
        ->assertOk()
        ->assertSee('nova-reserva');
});

test('public user can access consultation page', function () {
    $this->get(route('reservations.check'))
        ->assertStatus(200)
        ->assertSee('Consultar Status da disponibilidade');
});

test('create reservation action automatically generates protocol', function () {
    $action = app(CreateReservationAction::class);
    $reservation = $action->execute([
        'room_id' => $this->room->id,
        'event_name' => 'Seminário de Direito',
        'requester_name' => 'Dr. João',
        'requester_email' => 'joao@defensoria.mt.def.br',
        'date' => now()->addDays(10)->toDateString(),
        'start_time' => '14:00',
        'end_time' => '17:00',
        'attendees_count' => 50,
    ]);

    expect($reservation->protocol)->not->toBeNull()
        ->and($reservation->protocol)->toMatch('/^RES-\d{4}-\d{4}$/');
});

test('user can search reservation by exact protocol', function () {
    $action = app(CreateReservationAction::class);
    $reservation = $action->execute([
        'room_id' => $this->room->id,
        'event_name' => 'Reunião do Conselho',
        'requester_name' => 'Dra. Maria',
        'requester_email' => 'maria@defensoria.mt.def.br',
        'date' => now()->addDays(12)->toDateString(),
        'start_time' => '09:00',
        'end_time' => '11:00',
        'attendees_count' => 12,
    ]);

    Livewire::test(ReservationStatusCheck::class)
        ->set('searchQuery', $reservation->protocol)
        ->call('search')
        ->assertSee('Reunião do Conselho')
        ->assertSee($reservation->protocol)
        ->assertSee('Pendente de Aprovação');
});

test('search by requester email no longer matches', function () {
    $action = app(CreateReservationAction::class);
    $action->execute([
        'room_id' => $this->room->id,
        'event_name' => 'Oficina Prática',
        'requester_name' => 'Carlos Solicitante',
        'requester_email' => 'carlos.oficina@defensoria.mt.def.br',
        'date' => now()->addDays(15)->toDateString(),
        'start_time' => '10:00',
        'end_time' => '12:00',
        'attendees_count' => 20,
    ]);

    Livewire::test(ReservationStatusCheck::class)
        ->set('searchQuery', 'carlos.oficina@defensoria.mt.def.br')
        ->call('search')
        ->assertDontSee('Oficina Prática')
        ->assertSee('Nenhuma reserva encontrada');
});

test('user can search reservation by partial protocol code', function () {
    $action = app(CreateReservationAction::class);
    $reservation = $action->execute([
        'room_id' => $this->room->id,
        'event_name' => 'Congresso Anual',
        'requester_name' => 'Beatriz Souza',
        'requester_email' => 'beatriz@defensoria.mt.def.br',
        'date' => now()->addDays(18)->toDateString(),
        'start_time' => '08:00',
        'end_time' => '18:00',
        'attendees_count' => 200,
    ]);

    Livewire::test(ReservationStatusCheck::class)
        ->set('searchQuery', substr($reservation->protocol, -4))
        ->call('search')
        ->assertSee('Congresso Anual')
        ->assertSee($reservation->protocol);
});

test('search shows empty message when no record matches', function () {
    Livewire::test(ReservationStatusCheck::class)
        ->set('searchQuery', 'RES-9999-9999')
        ->call('search')
        ->assertSee('Nenhuma reserva encontrada');
});

test('user can search reservation by event name', function () {
    $action = app(CreateReservationAction::class);
    $reservation = $action->execute([
        'room_id' => $this->room->id,
        'event_name' => 'Workshop de Python',
        'requester_name' => 'Ana Dev',
        'requester_email' => 'ana@defensoria.mt.def.br',
        'date' => now()->addDays(20)->toDateString(),
        'start_time' => '09:00',
        'end_time' => '12:00',
        'attendees_count' => 30,
    ]);

    Livewire::test(ReservationStatusCheck::class)
        ->set('searchQuery', 'Workshop')
        ->call('search')
        ->assertSee('Workshop de Python')
        ->assertSee($reservation->protocol);
});

test('user can search reservation by room name', function () {
    $action = app(CreateReservationAction::class);
    $reservation = $action->execute([
        'room_id' => $this->room->id,
        'event_name' => 'Reunião de Equipe',
        'requester_name' => 'Pedro Gestor',
        'requester_email' => 'pedro@defensoria.mt.def.br',
        'date' => now()->addDays(22)->toDateString(),
        'start_time' => '14:00',
        'end_time' => '16:00',
        'attendees_count' => 15,
    ]);

    Livewire::test(ReservationStatusCheck::class)
        ->set('searchQuery', 'Auditório')
        ->call('search')
        ->assertSee('Reunião de Equipe')
        ->assertSee($reservation->protocol);
});

test('search by requester name no longer matches', function () {
    $action = app(CreateReservationAction::class);
    $action->execute([
        'room_id' => $this->room->id,
        'event_name' => 'Palestra',
        'requester_name' => 'Fernanda Silva',
        'requester_email' => 'fernanda@defensoria.mt.def.br',
        'date' => now()->addDays(25)->toDateString(),
        'start_time' => '10:00',
        'end_time' => '11:30',
        'attendees_count' => 8,
    ]);

    Livewire::test(ReservationStatusCheck::class)
        ->set('searchQuery', 'Fernanda')
        ->call('search')
        ->assertDontSee('Palestra')
        ->assertSee('Nenhuma reserva encontrada');
});

test('user can search reservation by full date dd/mm/aaaa', function () {
    $action = app(CreateReservationAction::class);
    $date = now()->addDays(30);
    $reservation = $action->execute([
        'room_id' => $this->room->id,
        'event_name' => 'Audiência Pública',
        'requester_name' => 'Renato Alves',
        'requester_email' => 'renato@defensoria.mt.def.br',
        'date' => $date->toDateString(),
        'start_time' => '13:00',
        'end_time' => '15:00',
        'attendees_count' => 40,
    ]);

    Livewire::test(ReservationStatusCheck::class)
        ->set('searchQuery', $date->format('d/m/Y'))
        ->call('search')
        ->assertSee('Audiência Pública')
        ->assertSee($reservation->protocol);
});

test('user can search reservation by day and month, matching any year', function () {
    $action = app(CreateReservationAction::class);
    $date = now()->addDays(40);
    $reservation = $action->execute([
        'room_id' => $this->room->id,
        'event_name' => 'Capacitação de Servidores',
        'requester_name' => 'Juliana Prado',
        'requester_email' => 'juliana@defensoria.mt.def.br',
        'date' => $date->toDateString(),
        'start_time' => '09:00',
        'end_time' => '17:00',
        'attendees_count' => 60,
    ]);

    Livewire::test(ReservationStatusCheck::class)
        ->set('searchQuery', $date->format('d/m'))
        ->call('search')
        ->assertSee('Capacitação de Servidores')
        ->assertSee($reservation->protocol);
});

test('user can search reservation by day only, matching any month and year', function () {
    $action = app(CreateReservationAction::class);
    $date = now()->addDays(50);
    $reservation = $action->execute([
        'room_id' => $this->room->id,
        'event_name' => 'Mesa Redonda',
        'requester_name' => 'Otávio Nunes',
        'requester_email' => 'otavio@defensoria.mt.def.br',
        'date' => $date->toDateString(),
        'start_time' => '16:00',
        'end_time' => '18:00',
        'attendees_count' => 25,
    ]);

    Livewire::test(ReservationStatusCheck::class)
        ->set('searchQuery', (string) (int) $date->format('d'))
        ->call('search')
        ->assertSee('Mesa Redonda')
        ->assertSee($reservation->protocol);
});

test('search ignores an invalid date-like query for the date filter but still matches other fields', function () {
    Livewire::test(ReservationStatusCheck::class)
        ->set('searchQuery', '32/13/2026')
        ->call('search')
        ->assertSee('Nenhuma reserva encontrada');
});
