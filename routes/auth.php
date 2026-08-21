<?php

use App\Models\Contact;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

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

    if (Auth::attempt($credentials, request()->boolean('remember'))) {
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
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();

    return redirect()->route('home');
})->name('logout')->middleware('auth');
