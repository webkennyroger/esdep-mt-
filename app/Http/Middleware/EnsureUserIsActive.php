<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Derruba a sessao de um usuario que teve o acesso revogado enquanto estava
 * logado. Sem isso, desativar alguem so teria efeito no proximo login.
 */
class EnsureUserIsActive
{
    /**
     * `last_login_at` so era gravado no handshake do Keycloak — com o cookie
     * "lembrar-me", a sessao sobrevive dias sem passar por ali de novo, e a
     * tela de Usuarios & Acessos mostrava um "ultimo acesso" bem mais antigo
     * do que o uso real. Aqui a gente atualiza a cada request autenticado,
     * limitado a uma vez cada 15min pra nao gravar no banco toda hora.
     */
    private const TOUCH_INTERVAL_MINUTES = 15;

    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::guard('web')->user();

        if ($user && ! $user->is_active) {
            Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('sso.pending')->with('sso_pending_name', $user->name);
        }

        if ($user && (! $user->last_login_at || $user->last_login_at->lt(now()->subMinutes(self::TOUCH_INTERVAL_MINUTES)))) {
            $user->forceFill(['last_login_at' => now()])->save();
        }

        return $next($request);
    }
}
