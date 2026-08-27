<?php

use App\Livewire\Admin\UserManager;
use App\Models\User;
use Livewire\Livewire;

test('somente quem tem manage-users acessa a tela de acessos', function () {
    $this->actingAs(User::factory()->admin()->create())
        ->get(route('admin.users.index'))->assertForbidden();

    $this->actingAs(User::factory()->superAdmin()->create())
        ->get(route('admin.users.index'))->assertOk();
});

test('a tela lista quem esta aguardando liberacao', function () {
    $pendente = User::factory()->pending()->create(['name' => 'Servidor Novo']);
    $ativo = User::factory()->admin()->create(['name' => 'Ja Liberado']);

    Livewire::actingAs(User::factory()->superAdmin()->create())
        ->test(UserManager::class)
        ->assertSee('Servidor Novo')
        ->assertSee('Ja Liberado')
        ->assertSet('roleChoice.'.$pendente->id, '')
        ->assertSet('roleChoice.'.$ativo->id, 'admin');
});

test('liberar um acesso ativa a conta e concede o perfil escolhido', function () {
    $superAdmin = User::factory()->superAdmin()->create();
    $pendente = User::factory()->pending()->create();

    Livewire::actingAs($superAdmin)
        ->test(UserManager::class)
        ->set('roleChoice.'.$pendente->id, 'admin')
        ->call('approve', $pendente->id);

    $pendente->refresh();
    expect($pendente->is_active)->toBeTrue()
        ->and($pendente->hasRole('admin'))->toBeTrue()
        ->and($pendente->activated_by_id)->toBe($superAdmin->id)
        ->and($pendente->activated_at)->not->toBeNull();
});

test('liberar sem perfil deixa a pessoa entrar mas sem area administrativa', function () {
    $pendente = User::factory()->pending()->create();

    Livewire::actingAs(User::factory()->superAdmin()->create())
        ->test(UserManager::class)
        ->set('roleChoice.'.$pendente->id, '')
        ->call('approve', $pendente->id);

    $pendente->refresh();
    expect($pendente->is_active)->toBeTrue()
        ->and($pendente->is_admin)->toBeFalse()
        ->and($pendente->roles()->count())->toBe(0);
});

test('um perfil fora da lista e recusado', function () {
    $pendente = User::factory()->pending()->create();

    Livewire::actingAs(User::factory()->superAdmin()->create())
        ->test(UserManager::class)
        ->set('roleChoice.'.$pendente->id, 'papel-inventado')
        ->call('approve', $pendente->id)
        ->assertStatus(422);

    expect($pendente->refresh()->is_active)->toBeFalse();
});

test('revogar um acesso derruba o usuario na proxima requisicao', function () {
    $alvo = User::factory()->admin()->create();

    Livewire::actingAs(User::factory()->superAdmin()->create())
        ->test(UserManager::class)
        ->call('revoke', $alvo->id);

    expect($alvo->refresh()->is_active)->toBeFalse();

    $this->actingAs($alvo)->get(route('dashboard'))->assertRedirect(route('sso.pending'));
});

test('um super admin nao consegue revogar o proprio acesso', function () {
    $superAdmin = User::factory()->superAdmin()->create();

    Livewire::actingAs($superAdmin)
        ->test(UserManager::class)
        ->call('revoke', $superAdmin->id);

    expect($superAdmin->refresh()->is_active)->toBeTrue();
});

test('um super admin nao consegue rebaixar a si mesmo', function () {
    $superAdmin = User::factory()->superAdmin()->create();

    Livewire::actingAs($superAdmin)
        ->test(UserManager::class)
        ->set('roleChoice.'.$superAdmin->id, 'admin')
        ->call('changeRole', $superAdmin->id);

    expect($superAdmin->refresh()->isSuperAdmin())->toBeTrue();
});

test('trocar o perfil de outro usuario substitui o papel anterior', function () {
    $alvo = User::factory()->admin()->create();

    Livewire::actingAs(User::factory()->superAdmin()->create())
        ->test(UserManager::class)
        ->set('roleChoice.'.$alvo->id, 'super-admin')
        ->call('changeRole', $alvo->id);

    $alvo->refresh();
    expect($alvo->isSuperAdmin())->toBeTrue()
        ->and($alvo->hasRole('admin'))->toBeFalse();
});
