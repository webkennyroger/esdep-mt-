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
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::guard('web')->user();

        if ($user && ! $user->is_active) {
            Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('sso.pending')->with('sso_pending_name', $user->name);
        }

        return $next($request);
    }
}
