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

test('can quick-create a contact from inside the create-group modal and it gets checked in the list', function () {
    $component = Livewire::test(ContactManager::class)
        ->call('openGroupModal')
        ->set('quickContactName', 'Contato Rapido')
        ->set('quickContactEmail', 'rapido@test.com')
        ->call('addQuickContact', 'groupContactIds')
        ->assertHasNoErrors();

    $contact = Contact::where('email', 'rapido@test.com')->firstOrFail();

    expect($component->get('groupContactIds'))->toBe([$contact->id])
        ->and($component->get('quickContactName'))->toBe('')
        ->and($component->get('quickContactEmail'))->toBe('');

    $component->assertSee('Contato Rapido');
});

test('quick-created contact ends up selected and can be saved with the group', function () {
    Livewire::test(ContactManager::class)
        ->call('openGroupModal')
        ->set('groupName', 'GRP')
        ->set('quickContactName', 'Fulano da Silva')
        ->set('quickContactEmail', 'fulano@test.com')
        ->call('addQuickContact', 'groupContactIds')
        ->call('createGroup')
        ->assertHasNoErrors();

    $contact = Contact::where('email', 'fulano@test.com')->firstOrFail();
    $group = ContactGroup::where('name', 'GRP')->firstOrFail();

    expect($group->contacts()->whereKey($contact->id)->exists())->toBeTrue();
});

test('admin can link a contact to an existing user picked from Usuarios & Acessos', function () {
    $existingUser = User::factory()->admin()->create(['name' => 'Fulano Servidor', 'email' => 'fulano.servidor@example.com']);

    $component = Livewire::test(ContactManager::class)
        ->call('openGroupModal')
        ->assertSee('Fulano Servidor')
        ->set('quickContactUserId', $existingUser->id)
        ->call('addContactFromUser', 'groupContactIds')
        ->assertHasNoErrors();

    $contact = Contact::where('email', 'fulano.servidor@example.com')->firstOrFail();

    expect($contact->name)->toBe('Fulano Servidor')
        ->and($contact->user_id)->toBe($existingUser->id)
        ->and($component->get('groupContactIds'))->toBe([$contact->id])
        ->and($component->get('quickContactUserId'))->toBeNull();
});

test('a user that already has a linked contact does not show up in the picker again', function () {
    $existingUser = User::factory()->admin()->create(['email' => 'ja-vinculado@example.com']);
    Contact::create(['name' => 'Ja Vinculado', 'email' => 'ja-vinculado@example.com', 'user_id' => $existingUser->id, 'is_active' => true]);

    Livewire::test(ContactManager::class)
        ->call('openGroupModal')
        // O contato ja existe e aparece na lista de selecao (checkbox) normalmente;
        // o que nao pode acontecer e o usuario aparecer de novo no <select> do topo,
        // que so lista quem ainda nao tem contato (opcao renderizada como "Nome (email)").
        ->assertDontSee('(ja-vinculado@example.com)');
});

test('quick-add rejects an unknown target property', function () {
    Livewire::test(ContactManager::class)
        ->call('openGroupModal')
        ->set('quickContactName', 'Alguem')
        ->set('quickContactEmail', 'alguem@test.com')
        ->call('addQuickContact', 'someOtherProperty')
        ->assertForbidden();
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
