<?php

use App\Models\User;

test('creating a user syncs the legacy role column into a real spatie role', function () {
    $user = User::factory()->create(['role' => 'admin']);

    expect($user->hasRole('admin'))->toBeTrue()
        ->and($user->hasRole('super-admin'))->toBeFalse()
        ->and($user->is_admin)->toBeTrue()
        ->and($user->isSuperAdmin())->toBeFalse();
});

test('both super_admin and super-admin legacy spellings map to the same spatie role', function () {
    $underscore = User::factory()->create(['role' => 'super_admin']);
    $hyphen = User::factory()->create(['role' => 'super-admin']);

    expect($underscore->hasRole('super-admin'))->toBeTrue()
        ->and($hyphen->hasRole('super-admin'))->toBeTrue()
        ->and($underscore->isSuperAdmin())->toBeTrue()
        ->and($hyphen->isSuperAdmin())->toBeTrue();
});

test('a plain user gets no admin role at all', function () {
    $user = User::factory()->create(['role' => 'user']);

    expect($user->is_admin)->toBeFalse()
        ->and($user->isSuperAdmin())->toBeFalse()
        ->and($user->roles()->count())->toBe(0);
});

test('changing role later updates the spatie role assignment', function () {
    $user = User::factory()->create(['role' => 'admin']);
    expect($user->isSuperAdmin())->toBeFalse();

    $user->update(['role' => 'super_admin']);

    expect($user->isSuperAdmin())->toBeTrue()
        ->and($user->hasRole('admin'))->toBeFalse();
});

test('admin role can manage rooms and reservations but not contacts, reports or activity log', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $this->actingAs($admin)->get(route('admin.rooms.index'))->assertOk();
    $this->actingAs($admin)->get(route('admin.contacts.index'))->assertForbidden();
    $this->actingAs($admin)->get(route('admin.reports'))->assertForbidden();
    $this->actingAs($admin)->get(route('admin.activity'))->assertForbidden();
});

test('super-admin can access every admin area including contacts, reports and activity log', function () {
    $superAdmin = User::factory()->create(['role' => 'super_admin']);

    $this->actingAs($superAdmin)->get(route('admin.rooms.index'))->assertOk();
    $this->actingAs($superAdmin)->get(route('admin.contacts.index'))->assertOk();
    $this->actingAs($superAdmin)->get(route('admin.reports'))->assertOk();
    $this->actingAs($superAdmin)->get(route('admin.activity'))->assertOk();
});

test('roles and permissions seeder creates the expected structure', function () {
    $this->seed(\Database\Seeders\RolesAndPermissionsSeeder::class);

    expect(\Spatie\Permission\Models\Role::where('name', 'admin')->exists())->toBeTrue()
        ->and(\Spatie\Permission\Models\Role::where('name', 'super-admin')->exists())->toBeTrue()
        ->and(\Spatie\Permission\Models\Role::findByName('super-admin', 'web')->permissions->pluck('name')->sort()->values()->all())
        ->toBe(['access-activity-log', 'access-contacts', 'access-reports']);
});
