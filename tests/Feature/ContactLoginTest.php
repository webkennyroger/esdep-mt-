<?php

use App\Livewire\Admin\ContactManager;
use App\Models\Contact;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;

test('administrator edit changes only cpf, cargo and sector - never name, email or password', function () {
    $admin = User::factory()->superAdmin()->create();
    $contact = Contact::create([
        'name' => 'Contato Antigo', 'email' => 'antigo@example.com',
        'password' => 'senha-antiga', 'is_active' => true,
    ]);

    Livewire::actingAs($admin)->test(ContactManager::class)
        ->call('openEditModal', $contact->id)
        ->set('editName', 'Contato Atualizado')
        ->set('editEmail', 'NOVO@EXAMPLE.COM')
        ->set('editPassword', 'nova-senha-segura')
        ->set('editCpf', '123.456.789-00')
        ->call('updateContact')
        ->assertHasNoErrors();

    $fresh = $contact->fresh();

    // Identidade vem do cadastro/SSO: a tela do admin nao troca nome, e-mail nem senha.
    expect($fresh->name)->toBe('Contato Antigo')
        ->and($fresh->email)->toBe('antigo@example.com')
        ->and(Hash::check('senha-antiga', $fresh->password))->toBeTrue()
        ->and($fresh->cpf)->toBe('123.456.789-00');

    Auth::guard('web')->logout();

    $this->post(route('login.post'), [
        'email' => 'antigo@example.com',
        'password' => 'senha-antiga',
    ])->assertRedirect(route('contact.dashboard'));

    $this->assertAuthenticatedAs($fresh, 'contact');
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
