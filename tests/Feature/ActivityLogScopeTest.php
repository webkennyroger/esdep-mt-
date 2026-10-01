<?php

use App\Livewire\Admin\ActivityLogController;
use App\Models\Contact;
use App\Models\Room;
use App\Models\User;
use App\Services\ActivityLogger;
use Livewire\Livewire;

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
