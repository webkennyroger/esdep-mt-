<?php

use App\Enums\ReservationStatus;
use App\Livewire\Admin\ReservationManager;
use App\Livewire\Public\BookingForm;
use App\Models\Reservation;
use App\Models\Room;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

function createBookingRoom(): Room
{
    return Room::factory()->create(['capacity' => 60]);
}

function createAdminForBooking(): User
{
    return User::factory()->create([
        'email' => 'admin@esdep.mt.gov.br',
        'password' => bcrypt('password'),
        'role' => 'admin',
    ]);
}

test('submit booking saves to database', function () {
    $room = createBookingRoom();
    $futureDate = now()->addDays(7)->format('Y-m-d');

    Livewire::test(BookingForm::class)
        ->set('requesterName', 'Joao da Silva')
        ->set('requesterEmail', 'joao@exemplo.gov.br')
        ->set('eventName', 'Reuniao de Trabalho')
        ->set('roomId', (string) $room->id)
        ->set('date', $futureDate)
        ->set('startTime', '09:00')
        ->set('endTime', '11:00')
        ->set('attendeesCount', 25)
        ->set('coffeeBreak', true)
        ->set('sonorizacao', false)
        ->set('transmissao', true)
        ->set('termsAccepted', true)
        ->call('submit')
        ->assertHasNoErrors()
        ->assertDispatched('reservationCreated');

    $reservation = Reservation::latest()->first();
    expect($reservation)->not->toBeNull();
    expect($reservation->event_name)->toBe('Reuniao de Trabalho');
    expect($reservation->requester_name)->toBe('Joao da Silva');
    expect($reservation->requester_email)->toBe('joao@exemplo.gov.br');
    expect($reservation->room_id)->toBe($room->id);
    expect(Carbon::parse($reservation->date)->format('Y-m-d'))->toBe($futureDate);
    expect($reservation->start_time)->toBe('09:00');
    expect($reservation->end_time)->toBe('11:00');
    expect($reservation->attendees_count)->toBe(25);
    expect($reservation->coffee_break)->toBeTrue();
    expect($reservation->sonorizacao)->toBeFalse();
    expect($reservation->transmissao)->toBeTrue();
    expect($reservation->status)->toBe(ReservationStatus::PENDING);
});

test('reservation appears in admin manager', function () {
    $room = createBookingRoom();
    $futureDate = now()->addDays(10)->format('Y-m-d');

    Reservation::factory()->create([
        'room_id' => $room->id,
        'event_name' => 'Workshop de Capacitacao',
        'requester_name' => 'Maria Santos',
        'requester_email' => 'maria@exemplo.gov.br',
        'date' => $futureDate,
        'start_time' => '14:00',
        'end_time' => '17:00',
        'attendees_count' => 40,
        'status' => ReservationStatus::PENDING,
    ]);

    $admin = createAdminForBooking();
    $this->actingAs($admin);

    Livewire::test(ReservationManager::class)
        ->assertSee('Workshop de Capacitacao')
        ->assertSee('Maria Santos');
});

test('all services saved correctly', function () {
    $room = createBookingRoom();
    $futureDate = now()->addDays(5)->format('Y-m-d');

    Livewire::test(BookingForm::class)
        ->set('requesterName', 'Pedro Almeida')
        ->set('requesterEmail', 'pedro@exemplo.gov.br')
        ->set('eventName', 'Seminario de Direitos')
        ->set('roomId', (string) $room->id)
        ->set('date', $futureDate)
        ->set('startTime', '08:30')
        ->set('endTime', '12:30')
        ->set('attendeesCount', 50)
        ->set('coffeeBreak', true)
        ->set('sonorizacao', true)
        ->set('transmissao', false)
        ->set('termsAccepted', true)
        ->call('submit')
        ->assertHasNoErrors()
        ->assertDispatched('reservationCreated');

    $reservation = Reservation::latest()->first();
    expect($reservation->coffee_break)->toBeTrue();
    expect($reservation->sonorizacao)->toBeTrue();
    expect($reservation->transmissao)->toBeFalse();
});

test('admin sees pending reservations', function () {
    $room = createBookingRoom();
    $admin = createAdminForBooking();

    Reservation::factory()->create([
        'room_id' => $room->id,
        'status' => ReservationStatus::PENDING,
        'event_name' => 'Reuniao Teste Visibilidade',
        'requester_name' => 'Carlos Teste',
    ]);

    $this->actingAs($admin);

    Livewire::test(ReservationManager::class)
        ->set('activeFilter', 'pending')
        ->assertSee('Reuniao Teste Visibilidade')
        ->assertSee('Carlos Teste')
        ->assertSee('Pendente');
});

test('admin can approve reservation', function () {
    $room = createBookingRoom();
    $admin = createAdminForBooking();

    $reservation = Reservation::factory()->create([
        'room_id' => $room->id,
        'status' => ReservationStatus::PENDING,
    ]);

    $this->actingAs($admin);

    Livewire::test(ReservationManager::class)
        ->call('openApproveModal', $reservation->id, $reservation->event_name)
        ->assertSet('showApproveModal', true)
        ->call('confirmApprove')
        ->assertSet('showApproveModal', false);

    $reservation->refresh();
    expect($reservation->status)->toBe(ReservationStatus::AUTHORIZED);
});

test('conflict is detected when room is already booked at same time', function () {
    $room = createBookingRoom();
    $futureDate = now()->addDays(7)->format('Y-m-d');

    Reservation::factory()->create([
        'room_id' => $room->id,
        'event_name' => 'Reuniao Existente',
        'date' => $futureDate,
        'start_time' => '09:00',
        'end_time' => '11:00',
        'status' => ReservationStatus::PENDING,
    ]);

    Livewire::test(BookingForm::class)
        ->set('requesterName', 'Joao da Silva')
        ->set('requesterEmail', 'joao@exemplo.gov.br')
        ->set('eventName', 'Reuniao Conflitante')
        ->set('roomId', (string) $room->id)
        ->set('date', $futureDate)
        ->set('startTime', '10:00')
        ->set('endTime', '12:00')
        ->set('attendeesCount', 10)
        ->set('termsAccepted', true)
        ->call('submit')
        ->assertHasNoErrors()
        ->assertNotDispatched('reservationCreated');

    expect(Reservation::where('event_name', 'Reuniao Conflitante')->exists())->toBeFalse();
});

test('terms not accepted blocks submission', function () {
    $room = createBookingRoom();
    $futureDate = now()->addDays(7)->format('Y-m-d');

    Livewire::test(BookingForm::class)
        ->set('requesterName', 'Joao da Silva')
        ->set('requesterEmail', 'joao@exemplo.gov.br')
        ->set('eventName', 'Reuniao Teste')
        ->set('roomId', (string) $room->id)
        ->set('date', $futureDate)
        ->set('startTime', '09:00')
        ->set('endTime', '11:00')
        ->set('attendeesCount', 10)
        ->set('termsAccepted', false)
        ->call('submit')
        ->assertHasErrors(['termsAccepted'])
        ->assertNotDispatched('reservationCreated');
});

test('attendees exceeding capacity blocks submission', function () {
    $room = createBookingRoom();
    $futureDate = now()->addDays(7)->format('Y-m-d');

    Livewire::test(BookingForm::class)
        ->set('requesterName', 'Joao da Silva')
        ->set('requesterEmail', 'joao@exemplo.gov.br')
        ->set('eventName', 'Reuniao Teste')
        ->set('roomId', (string) $room->id)
        ->set('date', $futureDate)
        ->set('startTime', '09:00')
        ->set('endTime', '11:00')
        ->set('attendeesCount', 999)
        ->set('termsAccepted', true)
        ->call('submit')
        ->assertNotDispatched('reservationCreated');
});

test('validated attachment is persisted with reservation', function () {
    Storage::fake('public');
    $room = createBookingRoom();

    Livewire::test(BookingForm::class)
        ->set('requesterName', 'Joao da Silva')
        ->set('requesterEmail', 'joao@exemplo.gov.br')
        ->set('eventName', 'Reserva com anexo')
        ->set('roomId', (string) $room->id)
        ->set('date', now()->addDays(7)->format('Y-m-d'))
        ->set('startTime', '09:00')
        ->set('endTime', '11:00')
        ->set('attendeesCount', 10)
        ->set('termsAccepted', true)
        ->set('attachment', UploadedFile::fake()->create('documento.pdf', 100, 'application/pdf'))
        ->call('submit')
        ->assertHasNoErrors();

    $reservation = Reservation::where('event_name', 'Reserva com anexo')->firstOrFail();
    expect($reservation->attachment_path)->not->toBeNull()
        ->and($reservation->attachment_name)->toBe('documento.pdf');
    Storage::disk('public')->assertExists($reservation->attachment_path);
});
