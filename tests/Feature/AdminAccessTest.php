<?php

use App\Models\User;

function createAdminUser(): User
{
    return User::factory()->create([
        'email' => 'admin@esdep.mt.gov.br',
        'password' => bcrypt('password'),
        'role' => 'admin',
    ]);
}

function createRegularUser(): User
{
    return User::factory()->create([
        'email' => 'user@esdep.mt.gov.br',
        'password' => bcrypt('password'),
        'role' => 'user',
    ]);
}

test('admin can access admin routes', function () {
    $admin = createAdminUser();

    $this->actingAs($admin)->get('/admin/reservations')->assertStatus(200);
    $this->actingAs($admin)->get('/admin/calendar')->assertStatus(200);
});

test('regular user cannot access admin routes', function () {
    $user = createRegularUser();

    $this->actingAs($user)->get('/admin/reservations')->assertStatus(403);
    $this->actingAs($user)->get('/admin/calendar')->assertStatus(403);
});

test('unauthenticated user cannot access admin routes', function () {
    $this->get('/admin/reservations')->assertRedirect('/login');
    $this->get('/admin/calendar')->assertRedirect('/login');
});

test('regular user can access dashboard', function () {
    $user = createRegularUser();

    $this->actingAs($user)->get('/dashboard')->assertStatus(200);
});
