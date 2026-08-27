<?php

namespace App\Http\Controllers\Auth;

use App\Actions\Auth\ResolveKeycloakUserAction;
use App\Exceptions\SsoAuthenticationException;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Laravel\Socialite\Facades\Socialite;
use SocialiteProviders\Keycloak\Provider as KeycloakProvider;
use Throwable;

class KeycloakController extends Controller
{
    /**
     * Chave de sessao com o id_token, exigido pelo Keycloak v18+ para que o
     * logout federado redirecione de volta para a aplicacao.
     */
    private const ID_TOKEN_SESSION_KEY = 'keycloak_id_token';

    public function redirect()
    {
        return $this->driver()->redirect();
    }

    /**
     * O contrato do Socialite nao expoe os metodos proprios do Keycloak
     * (getLogoutUrl, entre outros), entao resolvemos o driver concreto.
     */
    private function driver(): KeycloakProvider
    {
        $driver = Socialite::driver('keycloak');

        if (! $driver instanceof KeycloakProvider) {
            throw SsoAuthenticationException::providerFailure();
        }

        return $driver;
    }

    public function callback(Request $request, ResolveKeycloakUserAction $resolveUser): RedirectResponse
    {
        // O usuario pode cancelar na tela do Keycloak; nesse caso volta com erro.
        if ($request->has('error')) {
            return Redirect::route('login')->withErrors([
                'email' => 'Login institucional cancelado.',
            ]);
        }

        try {
            $ssoUser = $this->driver()->user();
        } catch (Throwable $e) {
            // A mensagem original pode conter o token; nunca exibir ao usuario.
            Log::error('Falha no callback do Keycloak', ['exception' => $e]);

            return Redirect::route('login')->withErrors([
                'email' => SsoAuthenticationException::providerFailure()->getMessage(),
            ]);
        }

        try {
            $user = $resolveUser->execute($ssoUser);
        } catch (SsoAuthenticationException $e) {
            return Redirect::route('login')->withErrors(['email' => $e->getMessage()]);
        }

        // Provisionado, porem ainda sem liberacao de um administrador.
        if (! $user->is_active) {
            return Redirect::route('sso.pending')->with('sso_pending_name', $user->name);
        }

        Auth::login($user, remember: true);
        $request->session()->regenerate();

        $request->session()->put(
            self::ID_TOKEN_SESSION_KEY,
            $ssoUser->accessTokenResponseBody['id_token'] ?? null
        );

        $user->forceFill(['last_login_at' => now()])->save();

        return Redirect::intended(route('dashboard'));
    }

    /**
     * Encerra a sessao local e a sessao no Keycloak (logout federado). Sem isso,
     * o proximo login entraria direto, sem pedir credenciais.
     */
    public function logout(Request $request): RedirectResponse
    {
        $idToken = $request->session()->get(self::ID_TOKEN_SESSION_KEY);

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        $logoutUrl = $this->driver()->getLogoutUrl(
            redirectUri: config('services.keycloak.logout_redirect') ?: config('app.url'),
            clientId: config('services.keycloak.client_id'),
            idTokenHint: $idToken,
        );

        return Redirect::away($logoutUrl);
    }
}
