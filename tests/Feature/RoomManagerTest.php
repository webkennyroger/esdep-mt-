<?php

use App\Livewire\Admin\RoomManager;
use App\Models\Reservation;
use App\Models\Room;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function () {
    $this->admin = User::factory()->create(['role' => 'admin']);
});

test('admin can access room manager page', function () {
    $this->actingAs($this->admin)
        ->get(route('admin.rooms.index'))
        ->assertStatus(200);
});

test('admin can create a new room', function () {
    Livewire::actingAs($this->admin)
        ->test(RoomManager::class)
        ->set('name', 'Nova Sala de Treinamento')
        ->set('capacity', 25)
        ->set('theme_color', '#2563eb')
        ->set('description', 'Sala equipada com 25 computadores')
        ->call('createRoom')
        ->assertHasNoErrors();

    expect(Room::where('name', 'Nova Sala de Treinamento')->value('code'))
        ->toBe('NOVA-SALA-DE-TREINAMENTO');
});

test('room code is generated uniquely', function () {
    Room::create([
        'name' => 'Sala Existente',
        'capacity' => 10,
        'theme_color' => '#008b5c',
        'is_active' => true,
    ]);

    $secondRoom = Room::create([
        'name' => 'Sala Existente',
        'capacity' => 15,
        'theme_color' => '#008b5c',
        'is_active' => true,
    ]);

    expect($secondRoom->code)->toBe('SALA-EXISTENTE-001');

    Livewire::actingAs($this->admin)
        ->test(RoomManager::class)
        ->set('name', 'Outra Sala')
        ->set('capacity', 15)
        ->set('theme_color', '#008b5c')
        ->call('createRoom')
        ->assertHasNoErrors();
});

test('admin can update a room', function () {
    $room = Room::create([
        'name' => 'Sala Antiga',
        'code' => 'ANT-01',
        'capacity' => 10,
        'theme_color' => '#008b5c',
        'is_active' => true,
    ]);

    Livewire::actingAs($this->admin)
        ->test(RoomManager::class)
        ->call('openEditModal', $room->id)
        ->set('editName', 'Sala Atualizada')
        ->set('editCapacity', 40)
        ->call('updateRoom')
        ->assertHasNoErrors();

    $room->refresh();
    expect($room->name)->toBe('Sala Atualizada')
        ->and($room->capacity)->toBe(40);
});

test('admin can toggle room active status', function () {
    $room = Room::create([
        'name' => 'Sala Teste',
        'code' => 'TST-01',
        'capacity' => 10,
        'theme_color' => '#008b5c',
        'is_active' => true,
    ]);

    Livewire::actingAs($this->admin)
        ->test(RoomManager::class)
        ->call('toggleActive', $room->id);

    $room->refresh();
    expect($room->is_active)->toBeFalse();
});

test('cannot delete room with existing reservations', function () {
    $room = Room::create([
        'name' => 'Sala com Reserva',
        'code' => 'RES-01',
        'capacity' => 10,
        'theme_color' => '#008b5c',
        'is_active' => true,
    ]);

    Reservation::create([
        'room_id' => $room->id,
        'event_name' => 'Evento Teste',
        'requester_name' => 'Solicitante',
        'requester_email' => 'solicitante@esdep.mt.gov.br',
        'date' => now()->addDays(5)->toDateString(),
        'start_time' => '09:00',
        'end_time' => '10:00',
        'attendees_count' => 5,
        'status' => 'pending',
    ]);

    Livewire::actingAs($this->admin)
        ->test(RoomManager::class)
        ->call('confirmDelete', $room->id)
        ->call('deleteRoom');

    expect(Room::where('id', $room->id)->exists())->toBeTrue();
});

test('can delete room without reservations', function () {
    $room = Room::create([
        'name' => 'Sala Sem Reserva',
        'code' => 'LIVRE-01',
        'capacity' => 10,
        'theme_color' => '#008b5c',
        'is_active' => true,
    ]);

    Livewire::actingAs($this->admin)
        ->test(RoomManager::class)
        ->call('confirmDelete', $room->id)
        ->call('deleteRoom');

    expect(Room::where('id', $room->id)->exists())->toBeFalse();
});

test('admin can define any valid room capacity when editing', function () {
    $room = Room::create([
        'name' => 'Sala com capacidade reservada',
        'code' => 'CAP-01',
        'capacity' => 50,
        'theme_color' => '#008b5c',
        'is_active' => true,
    ]);
    Reservation::create([
        'room_id' => $room->id,
        'event_name' => 'Evento grande',
        'requester_name' => 'Solicitante',
        'requester_email' => 'solicitante@esdep.mt.gov.br',
        'date' => now()->addDays(5)->toDateString(),
        'start_time' => '09:00',
        'end_time' => '10:00',
        'attendees_count' => 40,
        'status' => 'pending',
    ]);

    Livewire::actingAs($this->admin)
        ->test(RoomManager::class)
        ->call('openEditModal', $room->id)
        ->set('editCapacity', 20)
        ->call('updateRoom')
        ->assertHasNoErrors();

    expect($room->fresh()->capacity)->toBe(20);
});
