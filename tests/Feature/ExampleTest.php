<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;

test('the application returns a successful response', function () {
    $response = $this->get('/');

    $response->assertStatus(200);
});

test('admin can login', function () {
    User::factory()->admin()->create([
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
