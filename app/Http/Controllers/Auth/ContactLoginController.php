<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ContactLoginController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::guard('contact')->check()) {
            return redirect()->route('contact.dashboard');
        }

        return view('auth.contact-login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::guard('contact')->attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            Auth::guard('contact')->user()->update([
                'last_login_at' => now(),
            ]);

            return redirect()->intended(route('contact.dashboard'));
        }

        return back()->withErrors([
            'email' => 'E-mail ou senha incorretos.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::guard('contact')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('contact.login');
    }
}
