<?php

use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Role;

test('a coluna legada role nao existe mais: o spatie e a unica fonte de verdade', function () {
    expect(Schema::hasColumn('users', 'role'))->toBeFalse();
});

test('o papel admin e uma atribuicao real do spatie', function () {
    $user = User::factory()->admin()->create();

    expect($user->hasRole('admin'))->toBeTrue()
        ->and($user->hasRole('super-admin'))->toBeFalse()
        ->and($user->is_admin)->toBeTrue()
        ->and($user->isSuperAdmin())->toBeFalse();
});

test('um usuario comum nao recebe papel algum', function () {
    $user = User::factory()->create();

    expect($user->is_admin)->toBeFalse()
        ->and($user->isSuperAdmin())->toBeFalse()
        ->and($user->roles()->count())->toBe(0);
});

test('mudar o papel pelo spatie sobrevive a um save do usuario', function () {
    // Regressao: um hook no model ressincronizava os papeis a partir da coluna
    // legada `role` a cada `save()`. Como o login grava `last_login_at`, qualquer
    // papel atribuido pela interface era desfeito no acesso seguinte.
    $user = User::factory()->admin()->create();

    $user->syncRoles(['super-admin']);
    $user->forceFill(['last_login_at' => now()])->save();
    $user->refresh();

    expect($user->isSuperAdmin())->toBeTrue()
        ->and($user->hasRole('admin'))->toBeFalse();
});

test('as migrations criam os papeis canonicos sem depender de seeder', function () {
    // O hook antigo criava os papeis sob demanda com findOrCreate; sem ele, um
    // banco recem-migrado precisa ja vir com a estrutura pronta.
    expect(Role::where('name', 'admin')->exists())->toBeTrue()
        ->and(Role::where('name', 'super-admin')->exists())->toBeTrue()
        ->and(Role::where('name', 'gerenciador')->exists())->toBeTrue()
        ->and(Role::findByName('admin', 'web')->permissions->pluck('name')->sort()->values()->all())
        ->toBe(['access-activity-log', 'access-contacts', 'access-reports'])
        ->and(Role::findByName('super-admin', 'web')->permissions->pluck('name')->sort()->values()->all())
        ->toBe(['access-activity-log', 'access-contacts', 'access-reports', 'manage-users'])
        ->and(Role::findByName('gerenciador', 'web')->permissions->pluck('name')->sort()->values()->all())
        ->toBe(['access-activity-log', 'access-reports']);
});

test('admin role can manage rooms, reservations, contacts, reports and activity log but not users', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)->get(route('admin.rooms.index'))->assertOk();
    $this->actingAs($admin)->get(route('admin.reservations.index'))->assertOk();
    $this->actingAs($admin)->get(route('admin.contacts.index'))->assertOk();
    $this->actingAs($admin)->get(route('admin.reports'))->assertOk();
    $this->actingAs($admin)->get(route('admin.activity'))->assertOk();
    $this->actingAs($admin)->get(route('admin.users.index'))->assertForbidden();
});

test('gerenciador so acessa reservas, relatorios e historico — nunca salas, contatos ou usuarios', function () {
    $gerenciador = User::factory()->create();
    $gerenciador->assignRole('gerenciador');

    $this->actingAs($gerenciador)->get(route('admin.reservations.index'))->assertOk();
    $this->actingAs($gerenciador)->get(route('admin.reports'))->assertOk();
    $this->actingAs($gerenciador)->get(route('admin.activity'))->assertOk();
    $this->actingAs($gerenciador)->get(route('admin.rooms.index'))->assertForbidden();
    $this->actingAs($gerenciador)->get(route('admin.contacts.index'))->assertForbidden();
    $this->actingAs($gerenciador)->get(route('admin.users.index'))->assertForbidden();
});

test('super-admin can access every admin area including contacts, reports and activity log', function () {
    $superAdmin = User::factory()->superAdmin()->create();

    $this->actingAs($superAdmin)->get(route('admin.rooms.index'))->assertOk();
    $this->actingAs($superAdmin)->get(route('admin.contacts.index'))->assertOk();
    $this->actingAs($superAdmin)->get(route('admin.reports'))->assertOk();
    $this->actingAs($superAdmin)->get(route('admin.activity'))->assertOk();
    $this->actingAs($superAdmin)->get(route('admin.users.index'))->assertOk();
});

test('roles and permissions seeder creates the expected structure', function () {
    $this->seed(RolesAndPermissionsSeeder::class);

    expect(Role::where('name', 'admin')->exists())->toBeTrue()
        ->and(Role::where('name', 'super-admin')->exists())->toBeTrue()
        ->and(Role::where('name', 'gerenciador')->exists())->toBeTrue()
        ->and(Role::findByName('admin', 'web')->permissions->pluck('name')->sort()->values()->all())
        ->toBe(['access-activity-log', 'access-contacts', 'access-reports'])
        ->and(Role::findByName('super-admin', 'web')->permissions->pluck('name')->sort()->values()->all())
        ->toBe(['access-activity-log', 'access-contacts', 'access-reports', 'manage-users']);
});
