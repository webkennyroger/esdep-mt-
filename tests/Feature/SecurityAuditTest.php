<?php

use App\Livewire\Admin\ContactManager;
use App\Models\Contact;
use App\Models\User;
use Livewire\Livewire;

test('diagnostic route does not exist', function () {
    $response = $this->get('/__diag');
    $response->assertStatus(404);
});

test('gerente cannot access the contatos do setor screen anymore', function () {
    $contact = Contact::create(['name' => 'Gerente', 'email' => 'gerente-sem-grupos@example.com', 'password' => 'password', 'is_active' => true]);

    $response = $this->actingAs($contact, 'contact')->get('/contato/grupos');

    $response->assertStatus(404);
});

test('login route has rate limiting active', function () {
    for ($i = 0; $i < 5; $i++) {
        $this->post('/login', [
            'email' => 'invalid@esdep.mt.gov.br',
            'password' => 'wrong-password',
        ]);
    }

    $response = $this->post('/login', [
        'email' => 'invalid@esdep.mt.gov.br',
        'password' => 'wrong-password',
    ]);

    $response->assertStatus(429);
});

test('contact login route has rate limiting active', function () {
    for ($i = 0; $i < 5; $i++) {
        $this->post('/contato/login', [
            'email' => 'invalid@esdep.mt.gov.br',
            'password' => 'wrong-password',
        ]);
    }

    $response = $this->post('/contato/login', [
        'email' => 'invalid@esdep.mt.gov.br',
        'password' => 'wrong-password',
    ]);

    $response->assertStatus(429);
});

test('contact import validates email format per line', function () {
    $admin = User::factory()->create(['role' => 'super_admin']);

    $csvData = "Valido Silva, valido@esdep.mt.gov.br\nInvalido Santos, email-sem-arroba\n";

    Livewire::actingAs($admin)
        ->test(ContactManager::class)
        ->set('importCsv', $csvData)
        ->call('importContacts');

    expect(Contact::where('email', 'valido@esdep.mt.gov.br')->exists())->toBeTrue()
        ->and(Contact::where('name', 'Invalido Santos')->exists())->toBeFalse();
});
