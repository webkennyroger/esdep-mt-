<?php

use App\Http\Controllers\Auth\KeycloakController;
use App\Models\Contact;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
| SSO institucional (Keycloak). O callback precisa continuar batendo com a
| URI cadastrada em Valid Redirect URIs no client `agenda` do realm.
*/
Route::get('auth/keycloak/redirect', [KeycloakController::class, 'redirect'])->name('sso.redirect');
Route::get('auth/keycloak/callback', [KeycloakController::class, 'callback'])->name('sso.callback');
Route::view('auth/aguardando-liberacao', 'auth.sso-pending')->name('sso.pending');

Route::get('login', function () {
    if (Auth::check()) {
        return redirect()->route('dashboard');
    }
    if (Auth::guard('contact')->check()) {
        return redirect()->route('contact.dashboard');
    }

    return view('auth.login');
})->name('login');

Route::post('login', function () {
    $credentials = request()->validate([
        'email' => 'required|email',
        'password' => 'required',
    ]);
    $credentials['email'] = strtolower(trim($credentials['email']));

    if (Auth::attempt($credentials + ['is_active' => true], request()->boolean('remember'))) {
        request()->session()->regenerate();

        return redirect()->intended(route('dashboard'));
    }

    $contact = Contact::whereRaw('LOWER(email) = ?', [$credentials['email']])
        ->where('is_active', true)
        ->first();

    if ($contact && Auth::guard('contact')->attempt([
        'email' => $contact->email,
        'password' => $credentials['password'],
        'is_active' => true,
    ], request()->boolean('remember'))) {
        request()->session()->regenerate();
        $contact->update(['last_login_at' => now()]);

        return redirect()->intended(route('contact.dashboard'));
    }

    return back()->withErrors([
        'email' => 'Credenciais inválidas.',
    ])->onlyInput('email');
})->name('login.post')->middleware('throttle:5,1');

Route::post('logout', function () {
    // Quem entrou pelo SSO precisa encerrar tambem a sessao no Keycloak, senao
    // o proximo login volta direto, sem passar pela tela de credenciais.
    $user = Auth::user();

    if ($user instanceof User && $user->isSsoUser()) {
        return app(KeycloakController::class)->logout(request());
    }

    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();

    return redirect()->route('home');
})->name('logout')->middleware('auth');
