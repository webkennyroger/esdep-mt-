<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;

test('the application returns a successful response', function () {
    $response = $this->get('/');

    $response->assertStatus(200);
});

// A senha local ficou restrita ao super-admin (break-glass); todos os demais
// entram pelo SSO institucional. Ver KeycloakSsoTest para os dois cenarios.
test('super-admin can login with password', function () {
    User::factory()->superAdmin()->create([
        'email' => 'admin@esdep.mt.gov.br',
        'password' => Hash::make('password'),
    ]);

    $response = $this->post('/login', [
        'email' => 'admin@esdep.mt.gov.br',
        'password' => 'password',
    ]);

    $response->assertRedirect('/dashboard');
    $this->assertAuthenticated();
});
