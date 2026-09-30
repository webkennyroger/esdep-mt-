<?php

use App\Livewire\Contact\ContactReports;
use App\Models\Contact;
use App\Models\ContactGroup;
use App\Models\Reservation;
use App\Models\Room;
use Livewire\Livewire;

test('contact report includes a reservation dated on the exact last day of the range', function () {
    $group = ContactGroup::create(['name' => 'GRUPO RELATORIO', 'color' => '#008b5c']);
    $room = Room::factory()->create(['name' => 'Sala Relatorio Limite', 'contact_group_id' => $group->id]);
    $contact = Contact::create(['name' => 'Gestor', 'email' => 'gestor-relatorio@example.com', 'password' => 'password', 'is_active' => true]);
    $contact->groups()->attach($group, ['is_responsible' => true]);

    // Mesma regressao do ContactCalendar/Reports: whereBetween contra a data
    // pura (sem whereDate) exclui reservas datadas exatamente no ultimo dia
    // do intervalo, porque a coluna guarda um datetime completo. byRoom so
    // lista salas com total > 0, entao a sala so aparece se a reserva foi
    // contada corretamente.
    Reservation::factory()->create([
        'room_id' => $room->id,
        'status' => 'authorized',
        'date' => now()->endOfMonth()->toDateString(),
    ]);

    Livewire::actingAs($contact, 'contact')->test(ContactReports::class)
        ->assertSee('Sala Relatorio Limite');
});
