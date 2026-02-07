<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use RuntimeException;

class LoginController extends Controller
{
    public function showLoginForm(): View
    {
        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        try {
            if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            $user = $request->user();
            // NEW – SAFE ADDITION: Guests go to guest dashboard instead of admin dashboard.
            if ($user && $user->hasRole('Guest')) {
                return redirect()->intended(route('guest.dashboard'));
            }

            return redirect()->intended(route('dashboard'));
            }
        } catch (RuntimeException $e) {
            Log::warning('Login password hash error: ' . $e->getMessage(), ['email' => $credentials['email']]);
            throw ValidationException::withMessages([
                'email' => [__('auth.failed')],
            ]);
        }

        throw ValidationException::withMessages([
            'email' => [__('auth.failed')],
        ]);
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}
