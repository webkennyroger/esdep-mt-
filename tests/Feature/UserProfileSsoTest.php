<?php

use App\Livewire\Admin\UserProfile;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;

test('admin without sso can change local password normally', function () {
    $admin = User::factory()->admin()->create(['password' => bcrypt('senha-atual')]);

    Livewire::actingAs($admin)
        ->test(UserProfile::class)
        ->assertSet('canChangeLocalPassword', true)
        ->set('currentPassword', 'senha-atual')
        ->set('newPassword', 'nova-senha-123')
        ->set('newPasswordConfirmation', 'nova-senha-123')
        ->call('updatePassword')
        ->assertHasNoErrors();

    expect(Hash::check('nova-senha-123', $admin->fresh()->password))->toBeTrue();
});

test('sso admin (non super-admin) cannot change local password', function () {
    $admin = User::factory()->admin()->create([
        'keycloak_id' => fake()->uuid(),
        'password' => null,
    ]);

    Livewire::actingAs($admin)
        ->test(UserProfile::class)
        ->assertSet('canChangeLocalPassword', false)
        ->assertDontSee('wire:submit="updatePassword"', false)
        ->assertSee('login institucional');
});

test('sso admin cannot bypass the guard by calling updatePassword directly', function () {
    $admin = User::factory()->admin()->create([
        'keycloak_id' => fake()->uuid(),
        'password' => null,
    ]);

    Livewire::actingAs($admin)
        ->test(UserProfile::class)
        ->set('currentPassword', 'qualquer')
        ->set('newPassword', 'nova-senha-123')
        ->set('newPasswordConfirmation', 'nova-senha-123')
        ->call('updatePassword')
        ->assertForbidden();
});

test('sso super-admin can still change the local break-glass password', function () {
    $superAdmin = User::factory()->superAdmin()->create([
        'keycloak_id' => fake()->uuid(),
        'password' => bcrypt('senha-atual'),
    ]);

    Livewire::actingAs($superAdmin)
        ->test(UserProfile::class)
        ->assertSet('canChangeLocalPassword', true)
        ->set('currentPassword', 'senha-atual')
        ->set('newPassword', 'nova-senha-123')
        ->set('newPasswordConfirmation', 'nova-senha-123')
        ->call('updatePassword')
        ->assertHasNoErrors();
});

test('sso account sees a warning about deletion not revoking institutional access', function () {
    $admin = User::factory()->admin()->create(['keycloak_id' => fake()->uuid(), 'password' => null]);

    Livewire::actingAs($admin)
        ->test(UserProfile::class)
        ->assertSee('não revoga seu acesso no Keycloak');
});
