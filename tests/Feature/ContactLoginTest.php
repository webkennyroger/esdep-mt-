<?php

use App\Livewire\Admin\ContactManager;
use App\Models\Contact;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Livewire;

test('contact updated by administrator can authenticate through main login', function () {
    $admin = User::factory()->create(['role' => 'super_admin']);
    $contact = Contact::create([
        'name' => 'Contato Antigo', 'email' => 'antigo@example.com',
        'password' => 'senha-antiga', 'is_active' => true,
    ]);

    Livewire::actingAs($admin)->test(ContactManager::class)
        ->call('openEditModal', $contact->id)
        ->set('editName', 'Contato Atualizado')
        ->set('editEmail', 'NOVO@EXAMPLE.COM')
        ->set('editPassword', 'nova-senha-segura')
        ->call('updateContact')
        ->assertHasNoErrors();

    Auth::guard('web')->logout();

    $this->post(route('login.post'), [
        'email' => 'novo@example.com',
        'password' => 'nova-senha-segura',
    ])->assertRedirect(route('contact.dashboard'));

    $this->assertAuthenticatedAs($contact->fresh(), 'contact');
});

test('contact login accepts email regardless of letter case', function () {
    $contact = Contact::create([
        'name' => 'Contato', 'email' => 'Contato@Example.com',
        'password' => 'senha-segura', 'is_active' => true,
    ]);

    $this->post(route('contact.login.submit'), [
        'email' => 'contato@example.com',
        'password' => 'senha-segura',
    ])->assertRedirect(route('contact.dashboard'));

    $this->assertAuthenticatedAs($contact, 'contact');
});

test('inactive contact remains blocked even with correct password', function () {
    Contact::create([
        'name' => 'Contato Inativo', 'email' => 'inativo@example.com',
        'password' => 'senha-segura', 'is_active' => false,
    ]);

    $this->post(route('contact.login.submit'), [
        'email' => 'inativo@example.com',
        'password' => 'senha-segura',
    ])->assertSessionHasErrors('email');

    $this->assertGuest('contact');
});
