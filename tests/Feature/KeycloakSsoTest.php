<?php

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use SocialiteProviders\Keycloak\Provider as KeycloakProvider;
use SocialiteProviders\Manager\OAuth2\User as SocialiteUser;

/**
 * Monta a identidade que o Keycloak devolveria no callback.
 */
function ssoUser(string $sub, string $email, string $name = 'Fulano de Tal', array $extraRaw = []): SocialiteUser
{
    $user = new SocialiteUser;
    $user->map(['id' => $sub, 'name' => $name, 'email' => $email, 'nickname' => 'fulano']);
    $user->setRaw(array_merge(['sub' => $sub, 'email' => $email, 'email_verified' => true], $extraRaw));
    $user->accessTokenResponseBody = ['id_token' => 'fake-id-token'];

    return $user;
}

/**
 * O controller resolve o driver concreto do Keycloak (para alcancar metodos que
 * o contrato do Socialite nao expoe), entao o mock precisa ser desse mesmo tipo.
 */
function mockSso(SocialiteUser $user): void
{
    $provider = Mockery::mock(KeycloakProvider::class);
    $provider->shouldReceive('user')->once()->andReturn($user);

    Socialite::shouldReceive('driver')->with('keycloak')->andReturn($provider);
}

test('a rota de redirect envia o usuario ao keycloak com o client correto', function () {
    $response = $this->get(route('sso.redirect'));

    $response->assertRedirect();
    $target = $response->headers->get('Location');
    parse_str(parse_url($target, PHP_URL_QUERY) ?? '', $query);

    expect($target)->toContain('/realms/'.config('services.keycloak.realms').'/protocol/openid-connect/auth')
        ->and($query['client_id'])->toBe(config('services.keycloak.client_id'))
        ->and($query['redirect_uri'])->toBe(config('services.keycloak.redirect'))
        ->and($query['state'])->not->toBeEmpty();
});

test('quem nunca usou o sistema e provisionado inativo e nao entra', function () {
    mockSso(ssoUser('kc-novo-1', 'novo.servidor@defensoria.mt.def.br', 'Novo Servidor'));

    $response = $this->get(route('sso.callback'));

    $response->assertRedirect(route('sso.pending'));
    expect(Auth::check())->toBeFalse();

    $user = User::where('keycloak_id', 'kc-novo-1')->first();
    expect($user)->not->toBeNull()
        ->and($user->is_active)->toBeFalse()
        ->and($user->email)->toBe('novo.servidor@defensoria.mt.def.br')
        ->and($user->password)->toBeNull();
});

test('um admin que ja existia e pareado pelo e-mail e preserva seu papel', function () {
    $admin = User::factory()->superAdmin()->create([
        'email' => 'admin.existente@defensoria.mt.def.br',
    ]);

    mockSso(ssoUser('kc-existente-1', 'admin.existente@defensoria.mt.def.br'));

    $response = $this->get(route('sso.callback'));

    $response->assertRedirect(route('dashboard'));

    $admin->refresh();
    expect(Auth::id())->toBe($admin->id)
        ->and($admin->keycloak_id)->toBe('kc-existente-1')
        ->and($admin->isSuperAdmin())->toBeTrue()
        ->and($admin->last_login_at)->not->toBeNull()
        ->and(User::count())->toBe(1); // pareou, nao duplicou
});

test('o vinculo sobrevive a troca de e-mail no realm, pois a chave e o sub', function () {
    $user = User::factory()->admin()->create([
        'email' => 'nome.antigo@defensoria.mt.def.br',
        'keycloak_id' => 'kc-estavel-1',
    ]);

    mockSso(ssoUser('kc-estavel-1', 'nome.novo@defensoria.mt.def.br'));

    $this->get(route('sso.callback'))->assertRedirect(route('dashboard'));

    $user->refresh();
    expect($user->email)->toBe('nome.novo@defensoria.mt.def.br')
        ->and(User::count())->toBe(1)
        ->and($user->hasRole('admin'))->toBeTrue();
});

test('email_verified false nao bloqueia o login, pois o realm provisiona via TI', function () {
    // Contas vindas do AD chegam com `email_verified: false` por artefato do
    // provisionamento. Como o realm nao permite auto-cadastro, o e-mail continua
    // confiavel e o usuario precisa conseguir entrar.
    $user = User::factory()->admin()->create(['email' => 'servidor@defensoria.mt.def.br']);

    mockSso(ssoUser('kc-ad-1', 'servidor@defensoria.mt.def.br', 'Servidor', ['email_verified' => false]));

    $this->get(route('sso.callback'))->assertRedirect(route('dashboard'));

    $user->refresh();
    expect($user->keycloak_id)->toBe('kc-ad-1')
        ->and(Auth::id())->toBe($user->id);
});

test('conta sem e-mail no realm e recusada', function () {
    mockSso(ssoUser('kc-sem-email', ''));

    $this->get(route('sso.callback'))->assertRedirect(route('login'));
    expect(Auth::check())->toBeFalse();
});

test('o logout de um usuario sso encerra tambem a sessao no keycloak', function () {
    $user = User::factory()->admin()->create(['keycloak_id' => 'kc-logout-1']);

    $response = $this->actingAs($user)->post(route('logout'));

    $target = $response->headers->get('Location');
    parse_str(parse_url($target, PHP_URL_QUERY) ?? '', $query);

    expect($target)->toContain('/protocol/openid-connect/logout')
        ->and($query['client_id'])->toBe(config('services.keycloak.client_id'))
        ->and($query['post_logout_redirect_uri'])->not->toBeEmpty()
        ->and(Auth::check())->toBeFalse();
});

test('o logout de um usuario local nao passa pelo keycloak', function () {
    $user = User::factory()->admin()->create(['keycloak_id' => null]);

    $this->actingAs($user)->post(route('logout'))->assertRedirect(route('home'));

    expect(Auth::check())->toBeFalse();
});

test('desativar um usuario logado derruba a sessao na proxima requisicao', function () {
    $user = User::factory()->admin()->create();

    $this->actingAs($user);
    $this->get(route('dashboard'))->assertOk();

    $user->forceFill(['is_active' => false])->save();

    $this->get(route('dashboard'))->assertRedirect(route('sso.pending'));
    expect(Auth::check())->toBeFalse();
});

test('a senha local sobrevive apenas para o super-admin (break-glass)', function () {
    User::factory()->superAdmin()->create([
        'email' => 'super@defensoria.mt.def.br',
        'password' => bcrypt('segredo123'),
    ]);

    $this->post(route('login.post'), [
        'email' => 'super@defensoria.mt.def.br',
        'password' => 'segredo123',
    ])->assertRedirect(route('dashboard'));

    expect(Auth::check())->toBeTrue();
});

test('um admin comum nao entra mais por senha, e direcionado ao sso', function () {
    User::factory()->admin()->create([
        'email' => 'admin.comum@defensoria.mt.def.br',
        'password' => bcrypt('segredo123'),
    ]);

    $this->post(route('login.post'), [
        'email' => 'admin.comum@defensoria.mt.def.br',
        'password' => 'segredo123',
    ])->assertSessionHasErrors('email');

    expect(Auth::check())->toBeFalse();
});

test('login por senha e recusado para conta inativa', function () {
    User::factory()->create([
        'email' => 'inativo@defensoria.mt.def.br',
        'password' => bcrypt('segredo123'),
        'is_active' => false,
    ]);

    $response = $this->post(route('login.post'), [
        'email' => 'inativo@defensoria.mt.def.br',
        'password' => 'segredo123',
    ]);

    $response->assertSessionHasErrors('email');
    expect(Auth::check())->toBeFalse();
});
