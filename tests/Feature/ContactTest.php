<?php

use App\Livewire\Admin\ContactManager;
use App\Models\Contact;
use App\Models\ContactGroup;
use App\Models\Reservation;
use App\Models\Room;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function () {
    $this->actingAs(User::factory()->superAdmin()->create());
});

test('create contact', function () {
    Livewire::test(ContactManager::class)
        ->set('newName', 'Carlos Souza')
        ->set('newEmail', 'carlos@test.com')
        ->call('createContact')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('contacts', [
        'name' => 'Carlos Souza',
        'email' => 'carlos@test.com',
        'is_active' => true,
    ]);
});

test('cannot create duplicate email', function () {
    Contact::create([
        'name' => 'Carlos',
        'email' => 'carlos@test.com',
        'is_active' => true,
    ]);

    Livewire::test(ContactManager::class)
        ->set('newName', 'Outro Carlos')
        ->set('newEmail', 'carlos@test.com')
        ->call('createContact')
        ->assertHasErrors(['newEmail']);
});

test('a contact with reservation history can still be deleted', function () {
    $contact = Contact::create([
        'name' => 'Carlos',
        'email' => 'carlos@test.com',
        'is_active' => true,
    ]);
    $room = Room::factory()->create();
    Reservation::create([
        'room_id' => $room->id,
        'event_name' => 'Evento',
        'requester_name' => 'Carlos',
        'requester_email' => 'carlos@test.com',
        'date' => now()->addDays(2),
        'start_time' => '09:00',
        'end_time' => '10:00',
        'attendees_count' => 5,
        'status' => 'pending',
    ]);

    Livewire::test(ContactManager::class)
        ->call('deleteContact', $contact->id);

    $this->assertDatabaseMissing('contacts', ['id' => $contact->id]);
    $this->assertDatabaseHas('reservations', ['requester_email' => 'carlos@test.com']);
});

test('create group', function () {
    Livewire::test(ContactManager::class)
        ->set('groupName', 'Defensores')
        ->set('groupDescription', 'Grupo de defensores')
        ->call('createGroup')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('contact_groups', ['name' => 'DEFENSORES']);
});

test('import contacts', function () {
    $csv = "nome,email\nCarlos Souza,carlos@test.com\nMaria Silva,maria@test.com";

    Livewire::test(ContactManager::class)
        ->set('importCsv', $csv)
        ->call('importContacts')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('contacts', ['email' => 'carlos@test.com']);
    $this->assertDatabaseHas('contacts', ['email' => 'maria@test.com']);
});

test('delete contact', function () {
    $contact = Contact::create([
        'name' => 'Carlos',
        'email' => 'carlos@test.com',
        'is_active' => true,
    ]);

    Livewire::test(ContactManager::class)
        ->call('deleteContact', $contact->id);

    $this->assertDatabaseMissing('contacts', ['id' => $contact->id]);
});

test('cargo gerente with a setor automatically grants responsibility', function () {
    $group = ContactGroup::create(['name' => 'SETOR Y', 'color' => '#059669']);

    Livewire::test(ContactManager::class)
        ->set('newName', 'Nova Gerente')
        ->set('newEmail', 'nova-gerente@test.com')
        ->set('newSetor', 'SETOR Y')
        ->set('newTipo', 'Gerente')
        ->call('createContact')
        ->assertHasNoErrors();

    $contact = Contact::where('email', 'nova-gerente@test.com')->firstOrFail();
    expect((bool) $contact->groups()->whereKey($group->id)->firstOrFail()->pivot->is_responsible)->toBeTrue();
});

test('a setor without cargo gerente does not grant responsibility', function () {
    $group = ContactGroup::create(['name' => 'SETOR Z', 'color' => '#059669']);

    Livewire::test(ContactManager::class)
        ->set('newName', 'Contato Comum')
        ->set('newEmail', 'contato-comum@test.com')
        ->set('newSetor', 'SETOR Z')
        ->call('createContact')
        ->assertHasNoErrors();

    $contact = Contact::where('email', 'contato-comum@test.com')->firstOrFail();
    expect((bool) $contact->groups()->whereKey($group->id)->firstOrFail()->pivot->is_responsible)->toBeFalse();
});

test('super-admin can create a contact with cargo admin which also creates a linked admin login', function () {
    Livewire::test(ContactManager::class)
        ->set('newName', 'Nova Admin')
        ->set('newEmail', 'nova-admin@test.com')
        ->set('newPassword', 'senha-segura')
        ->set('newTipo', 'Admin')
        ->call('createContact')
        ->assertHasNoErrors();

    $contact = Contact::where('email', 'nova-admin@test.com')->firstOrFail();
    expect($contact->user_id)->not->toBeNull();

    $user = User::find($contact->user_id);
    expect($user)->not->toBeNull()
        ->and($user->email)->toBe('nova-admin@test.com')
        ->and($user->is_admin)->toBeTrue()
        ->and($user->isSuperAdmin())->toBeFalse();
});

test('cargo admin requires a password since it creates a real login', function () {
    Livewire::test(ContactManager::class)
        ->set('newName', 'Sem Senha')
        ->set('newEmail', 'sem-senha@test.com')
        ->set('newTipo', 'Admin')
        ->call('createContact')
        ->assertHasErrors(['newPassword']);
});

test('a plain admin cannot create a contact with cargo admin', function () {
    $admin = User::factory()->admin()->create();

    Livewire::actingAs($admin)->test(ContactManager::class)
        ->set('newName', 'Tentativa')
        ->set('newEmail', 'tentativa@test.com')
        ->set('newPassword', 'senha-segura')
        ->set('newTipo', 'Admin')
        ->call('createContact');
})->throws(Exception::class);

test('changing a contact cargo to admin later creates the linked login', function () {
    $contact = Contact::create(['name' => 'Vira Admin', 'email' => 'vira-admin@test.com', 'is_active' => true]);

    Livewire::test(ContactManager::class)
        ->call('openEditModal', $contact->id)
        ->set('editTipo', 'Admin')
        ->set('editPassword', 'senha-segura')
        ->call('updateContact')
        ->assertHasNoErrors();

    expect($contact->fresh()->user_id)->not->toBeNull();
    expect(User::find($contact->fresh()->user_id)->email)->toBe('vira-admin@test.com');
});

test('changing a contact cargo away from admin deletes the linked login', function () {
    $contact = Contact::create(['name' => 'Deixa De Ser Admin', 'email' => 'deixa-admin@test.com', 'is_active' => true]);

    Livewire::test(ContactManager::class)
        ->call('openEditModal', $contact->id)
        ->set('editTipo', 'Admin')
        ->set('editPassword', 'senha-segura')
        ->call('updateContact')
        ->assertHasNoErrors();

    $adminUserId = $contact->fresh()->user_id;
    expect($adminUserId)->not->toBeNull();

    Livewire::test(ContactManager::class)
        ->call('openEditModal', $contact->id)
        ->set('editTipo', 'Gerente')
        ->call('updateContact')
        ->assertHasNoErrors();

    expect($contact->fresh()->user_id)->toBeNull();
    expect(User::find($adminUserId))->toBeNull();
});

test('deleting a contact with cargo admin also removes the linked login', function () {
    $contact = Contact::create(['name' => 'Admin Para Excluir', 'email' => 'admin-excluir@test.com', 'is_active' => true]);

    Livewire::test(ContactManager::class)
        ->call('openEditModal', $contact->id)
        ->set('editTipo', 'Admin')
        ->set('editPassword', 'senha-segura')
        ->call('updateContact')
        ->assertHasNoErrors();

    $adminUserId = $contact->fresh()->user_id;

    Livewire::test(ContactManager::class)
        ->call('deleteContact', $contact->id);

    $this->assertDatabaseMissing('contacts', ['id' => $contact->id]);
    expect(User::find($adminUserId))->toBeNull();
});
