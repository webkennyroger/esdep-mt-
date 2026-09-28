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

test('contact sees the attachment of their own reservation when editing it', function () {
    $room = Room::create(['name' => 'Sala Anexo', 'code' => 'ANX', 'capacity' => 10, 'is_active' => true]);
    $contact = Contact::create(['name' => 'Contato Anexo', 'email' => 'anexo@example.com', 'password' => 'password', 'is_active' => true]);
    $reservation = Reservation::create([
        'room_id' => $room->id, 'event_name' => 'Evento com Anexo', 'requester_name' => 'Contato Anexo',
        'requester_email' => 'anexo@example.com', 'date' => now()->addDays(2),
        'start_time' => '09:00', 'end_time' => '10:00', 'attendees_count' => 5, 'status' => 'pending',
        'attachment_path' => 'attachments/recibo.pdf', 'attachment_name' => 'recibo.pdf',
    ]);

    Livewire::actingAs($contact, 'contact')
        ->test(ContactReservationEdit::class, ['reservation' => $reservation->id])
        ->assertSee('recibo.pdf')
        ->assertSet('hasAttachment', true);
});

test('contact sees "no attachment" message when reservation has none', function () {
    $room = Room::create(['name' => 'Sala Sem Anexo', 'code' => 'SANX', 'capacity' => 10, 'is_active' => true]);
    $contact = Contact::create(['name' => 'Contato Sem Anexo', 'email' => 'sem-anexo@example.com', 'password' => 'password', 'is_active' => true]);
    $reservation = Reservation::create([
        'room_id' => $room->id, 'event_name' => 'Evento Sem Anexo', 'requester_name' => 'Contato Sem Anexo',
        'requester_email' => 'sem-anexo@example.com', 'date' => now()->addDays(2),
        'start_time' => '09:00', 'end_time' => '10:00', 'attendees_count' => 5, 'status' => 'pending',
    ]);

    Livewire::actingAs($contact, 'contact')
        ->test(ContactReservationEdit::class, ['reservation' => $reservation->id])
        ->assertSee('Nenhum anexo enviado')
        ->assertSet('hasAttachment', false);
});
