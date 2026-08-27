<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;

test('cria um super-administrador ativo e com senha', function () {
    $this->artisan('user:super-admin', [
        '--email' => 'Chefe@Defensoria.MT.def.br',
        '--name' => 'Chefe da Escola',
        '--password' => 'senha-bem-longa-2026',
    ])->assertSuccessful();

    $user = User::whereRaw('LOWER(email) = ?', ['chefe@defensoria.mt.def.br'])->firstOrFail();

    expect($user->isSuperAdmin())->toBeTrue()
        ->and($user->is_active)->toBeTrue()
        ->and($user->name)->toBe('Chefe da Escola')
        ->and($user->email)->toBe('chefe@defensoria.mt.def.br') // normalizado
        ->and(Hash::check('senha-bem-longa-2026', $user->password))->toBeTrue();
});

test('promove uma conta existente sem perder o vinculo com o sso', function () {
    $existente = User::factory()->admin()->create([
        'email' => 'ja.existe@defensoria.mt.def.br',
        'keycloak_id' => 'kc-preservado-1',
    ]);

    $this->artisan('user:super-admin', ['--email' => 'ja.existe@defensoria.mt.def.br', '--no-interaction' => true])
        ->assertSuccessful();

    $existente->refresh();
    expect($existente->isSuperAdmin())->toBeTrue()
        ->and($existente->hasRole('admin'))->toBeFalse()
        ->and($existente->keycloak_id)->toBe('kc-preservado-1')
        ->and(User::count())->toBe(1); // promoveu, nao duplicou
});

test('reativa uma conta que estava aguardando liberacao', function () {
    $pendente = User::factory()->pending()->create(['email' => 'parado@defensoria.mt.def.br']);

    $this->artisan('user:super-admin', ['--email' => 'parado@defensoria.mt.def.br', '--no-interaction' => true])
        ->assertSuccessful();

    expect($pendente->refresh()->is_active)->toBeTrue();
});

test('recusa senha curta demais', function () {
    $this->artisan('user:super-admin', [
        '--email' => 'fraco@defensoria.mt.def.br',
        '--name' => 'Senha Fraca',
        '--password' => 'curta1',
    ])->assertFailed();

    expect(User::where('email', 'fraco@defensoria.mt.def.br')->exists())->toBeFalse();
});

test('recusa e-mail invalido', function () {
    $this->artisan('user:super-admin', [
        '--email' => 'nao-e-um-email',
        '--name' => 'Invalido',
        '--password' => 'senha-bem-longa-2026',
    ])->assertFailed();

    expect(User::count())->toBe(0);
});

test('sem senha a conta e criada apenas para login pelo sso', function () {
    $this->artisan('user:super-admin', [
        '--email' => 'so.sso@defensoria.mt.def.br',
        '--name' => 'Apenas SSO',
        '--no-interaction' => true,
    ])->assertSuccessful();

    $user = User::where('email', 'so.sso@defensoria.mt.def.br')->firstOrFail();

    expect($user->password)->toBeNull()
        ->and($user->isSuperAdmin())->toBeTrue()
        ->and($user->is_active)->toBeTrue();
});
