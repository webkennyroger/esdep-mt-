<?php

use App\Livewire\Contact\ContactReservationEdit;
use App\Models\Contact;
use App\Models\Reservation;
use App\Models\Room;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Livewire\Livewire;

test('contact cannot replace reservation id and edit another contacts reservation', function () {
    $room = Room::create(['name' => 'Sala Segura', 'code' => 'SEG', 'capacity' => 10, 'is_active' => true]);
    $contact = Contact::create(['name' => 'Contato A', 'email' => 'a@example.com', 'password' => 'password', 'is_active' => true]);
    $own = Reservation::create([
        'room_id' => $room->id, 'event_name' => 'Evento A', 'requester_name' => 'A',
        'requester_email' => 'a@example.com', 'date' => now()->addDays(2),
        'start_time' => '09:00', 'end_time' => '10:00', 'attendees_count' => 5, 'status' => 'pending',
    ]);
    $foreign = Reservation::create([
        'room_id' => $room->id, 'event_name' => 'Evento B', 'requester_name' => 'B',
        'requester_email' => 'b@example.com', 'date' => now()->addDays(3),
        'start_time' => '09:00', 'end_time' => '10:00', 'attendees_count' => 5, 'status' => 'pending',
    ]);

    expect(fn () => Livewire::actingAs($contact, 'contact')
        ->test(ContactReservationEdit::class, ['reservation' => $own->id])
        ->set('reservationId', $foreign->id)
        ->set('eventName', 'Ataque')
        ->call('submit'))
        ->toThrow(ModelNotFoundException::class);

    expect($foreign->fresh()->event_name)->toBe('Evento B');
});
