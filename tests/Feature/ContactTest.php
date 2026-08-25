<?php

use App\Livewire\Admin\ContactManager;
use App\Models\Contact;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function () {
    $this->actingAs(User::factory()->create(['role' => 'super_admin']));
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

test('toggle contact active', function () {
    $contact = Contact::create([
        'name' => 'Carlos',
        'email' => 'carlos@test.com',
        'is_active' => true,
        'subscribed_at' => now(),
    ]);

    Livewire::test(ContactManager::class)
        ->call('toggleActive', $contact->id);

    $contact->refresh();
    expect($contact->is_active)->toBeFalse();
    expect($contact->unsubscribed_at)->not->toBeNull();
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

test('only super-admin sees the papel column identifying gerentes', function () {
    $group = \App\Models\ContactGroup::create(['name' => 'SETOR X', 'color' => '#059669']);
    $gerente = Contact::create(['name' => 'Gestora', 'email' => 'gestora@test.com', 'is_active' => true]);
    $gerente->groups()->attach($group, ['is_responsible' => true]);

    Livewire::test(ContactManager::class)
        ->assertSee('Papel')
        ->assertSee('Gerente');

    $admin = User::factory()->create(['role' => 'admin']);
    Livewire::actingAs($admin)->test(ContactManager::class)
        ->assertDontSee('Papel');
});

test('super-admin can mark a contact as gerente responsavel when creating with a setor', function () {
    $group = \App\Models\ContactGroup::create(['name' => 'SETOR Y', 'color' => '#059669']);

    Livewire::test(ContactManager::class)
        ->set('newName', 'Nova Gerente')
        ->set('newEmail', 'nova-gerente@test.com')
        ->set('newSetor', 'SETOR Y')
        ->set('newIsResponsible', true)
        ->call('createContact')
        ->assertHasNoErrors();

    $contact = Contact::where('email', 'nova-gerente@test.com')->firstOrFail();
    expect((bool) $contact->groups()->whereKey($group->id)->firstOrFail()->pivot->is_responsible)->toBeTrue();
});

test('selecting a setor without marking gerente responsavel does not grant responsibility', function () {
    $group = \App\Models\ContactGroup::create(['name' => 'SETOR Z', 'color' => '#059669']);

    Livewire::test(ContactManager::class)
        ->set('newName', 'Contato Comum')
        ->set('newEmail', 'contato-comum@test.com')
        ->set('newSetor', 'SETOR Z')
        ->call('createContact')
        ->assertHasNoErrors();

    $contact = Contact::where('email', 'contato-comum@test.com')->firstOrFail();
    expect((bool) $contact->groups()->whereKey($group->id)->firstOrFail()->pivot->is_responsible)->toBeFalse();
});
