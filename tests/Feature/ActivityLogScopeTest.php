<?php

use App\Livewire\Admin\ActivityLogController;
use App\Models\Contact;
use App\Models\ContactGroup;
use App\Models\Reservation;
use App\Models\Room;
use App\Models\User;
use App\Services\ActivityLogger;
use Livewire\Livewire;

/**
 * O gerenciador de setor so pode ver o historico da propria sala — nada de
 * contatos, grupos ou usuarios de outros setores.
 */
function gerenciadorDoSetor(): array
{
    $group = ContactGroup::create(['name' => 'SETOR ATIVIDADES', 'color' => '#008b5c']);
    $room = Room::factory()->create(['contact_group_id' => $group->id]);

    $gerenciador = User::factory()->create();
    $gerenciador->assignRole('gerenciador');
    $contact = Contact::create([
        'name' => 'Gerenciador', 'email' => $gerenciador->email,
        'password' => 'password', 'is_active' => true, 'user_id' => $gerenciador->id,
    ]);
    $contact->groups()->attach($group);

    return [$gerenciador->fresh(), $room];
}

test('gerenciador so ve o historico de atividades da propria sala', function () {
    [$gerenciador, $minhaSala] = gerenciadorDoSetor();
    $outraSala = Room::factory()->create();

    ActivityLogger::roomCreated($minhaSala);
    ActivityLogger::roomCreated($outraSala);

    $minhaReserva = Reservation::create([
        'room_id' => $minhaSala->id, 'event_name' => 'Evento do Setor', 'requester_name' => 'Alguem',
        'requester_email' => 'alguem@example.com', 'date' => now()->addDays(2),
        'start_time' => '09:00', 'end_time' => '10:00', 'attendees_count' => 5, 'status' => 'pending',
    ]);
    ActivityLogger::reservationCreated($minhaReserva);

    $outraReserva = Reservation::create([
        'room_id' => $outraSala->id, 'event_name' => 'Evento de Outro Setor', 'requester_name' => 'Alguem',
        'requester_email' => 'outro@example.com', 'date' => now()->addDays(2),
        'start_time' => '09:00', 'end_time' => '10:00', 'attendees_count' => 5, 'status' => 'pending',
    ]);
    ActivityLogger::reservationCreated($outraReserva);

    $contatoQualquer = Contact::create(['name' => 'Contato Qualquer', 'email' => 'contato-qualquer-log@example.com', 'is_active' => true]);
    ActivityLogger::created($contatoQualquer, 'Contato criado');

    Livewire::actingAs($gerenciador)->test(ActivityLogController::class)
        ->assertSee('Sala &quot;'.$minhaSala->name.'&quot; criada', false)
        ->assertDontSee($outraSala->name)
        ->assertSee('Evento do Setor')
        ->assertDontSee('Evento de Outro Setor')
        ->assertDontSee('Contato Qualquer');
});

test('admin e super-admin veem o historico completo, sem filtro de setor', function () {
    $room = Room::factory()->create();
    ActivityLogger::roomCreated($room);
    $contact = Contact::create(['name' => 'Contato Global', 'email' => 'contato-global-log@example.com', 'is_active' => true]);
    ActivityLogger::created($contact, "Contato \"{$contact->name}\" criado");

    Livewire::actingAs(User::factory()->admin()->create())->test(ActivityLogController::class)
        ->assertSee($room->name)
        ->assertSee('Contato Global');

    Livewire::actingAs(User::factory()->superAdmin()->create())->test(ActivityLogController::class)
        ->assertSee($room->name)
        ->assertSee('Contato Global');
});
