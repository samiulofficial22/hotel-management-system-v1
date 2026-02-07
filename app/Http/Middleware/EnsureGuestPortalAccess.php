<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureGuestPortalAccess
{
    /**
     * If the user has Guest role but no linked guest profile (portal access revoked),
     * logout and redirect to login page.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();
        if ($user && $user->hasRole('Guest') && ! $user->guest) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')
                ->with('error', __('Your portal access has been revoked. Please contact the hotel.'));
        }

        return $next($request);
    }
}
