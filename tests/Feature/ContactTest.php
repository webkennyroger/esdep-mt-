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

test('the create contact button was removed from the page, only the group button remains', function () {
    Livewire::test(ContactManager::class)
        ->assertDontSee('Novo contato')
        ->assertSee('Novo Grupo');
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

test('cargo assessor is just an informative label with no login side effect', function () {
    Livewire::test(ContactManager::class)
        ->set('newName', 'Novo Assessor')
        ->set('newEmail', 'novo-assessor@test.com')
        ->set('newPassword', 'senha-segura')
        ->set('newTipo', 'Assessor')
        ->call('createContact')
        ->assertHasNoErrors();

    $contact = Contact::where('email', 'novo-assessor@test.com')->firstOrFail();
    expect($contact->tipo)->toBe('Assessor')
        ->and($contact->user_id)->toBeNull();
});

test('any user can pick any cargo, including assessor, without the manage-users permission', function () {
    $user = User::factory()->superAdmin()->create();
    $user->revokePermissionTo('manage-users');

    Livewire::actingAs($user)->test(ContactManager::class)
        ->set('newName', 'Contato Comum')
        ->set('newEmail', 'contato-comum@test.com')
        ->set('newTipo', 'Assessor')
        ->call('createContact')
        ->assertHasNoErrors();

    expect(Contact::where('email', 'contato-comum@test.com')->firstOrFail()->tipo)->toBe('Assessor');
});

test('changing a contact cargo never creates or removes a linked login', function () {
    $contact = Contact::create(['name' => 'Contato Qualquer', 'email' => 'contato-qualquer@test.com', 'is_active' => true]);

    Livewire::test(ContactManager::class)
        ->call('openEditModal', $contact->id)
        ->set('editTipo', 'Assessor')
        ->call('updateContact')
        ->assertHasNoErrors();

    expect($contact->fresh()->user_id)->toBeNull();
});
