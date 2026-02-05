<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Guest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class GuestPortalController extends Controller
{
    public function dashboard(Request $request): View
    {
        $user = $request->user();
        if (! $user || ! $user->hasRole('Guest')) {
            abort(403);
        }
        $guest = $user->guest;

        $upcomingBookings = collect();
        $pastBookings = collect();

        if ($guest) {
            $baseQuery = Booking::with('room')
                ->where('guest_id', $guest->id)
                ->orderByDesc('check_in_date');

            $upcomingBookings = (clone $baseQuery)
                ->whereDate('check_in_date', '>=', now()->toDateString())
                ->take(5)
                ->get();

            $pastBookings = (clone $baseQuery)
                ->whereDate('check_in_date', '<', now()->toDateString())
                ->take(5)
                ->get();
        }

        return view('guest.dashboard', compact('guest', 'upcomingBookings', 'pastBookings'));
    }

    public function profile(Request $request): View
    {
        $user = $request->user();
        if (! $user || ! $user->hasRole('Guest')) {
            abort(403);
        }
        /** @var Guest|null $guest */
        $guest = optional($user)->guest;

        return view('guest.profile', compact('guest', 'user'));
    }

    /** NEW – SAFE ADDITION: Allow guest to update own profile (basic fields) and user name/email. */
    public function updateProfile(Request $request): RedirectResponse
    {
        $user = $request->user();
        /** @var Guest|null $guest */
        $guest = optional($user)->guest;
        if (! $guest) {
            abort(403);
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'nationality' => ['nullable', 'string', 'max:100'],
            'address' => ['nullable', 'string'],
            'city' => ['nullable', 'string', 'max:100'],
            'country' => ['nullable', 'string', 'max:100'],
        ]);

        // Update user basic info
        $user->name = $data['name'];
        if (! empty($data['email'])) {
            $user->email = $data['email'];
        }
        $user->save();

        // Update guest contact info
        $guest->phone = $data['phone'] ?? $guest->phone;
        $guest->nationality = $data['nationality'] ?? $guest->nationality;
        $guest->address = $data['address'] ?? $guest->address;
        $guest->city = $data['city'] ?? $guest->city;
        $guest->country = $data['country'] ?? $guest->country;
        $guest->save();

        return redirect()->route('guest.profile')->with('success', __('Profile updated.'));
    }

    /** NEW – SAFE ADDITION: Allow guest to change own password. */
    public function updatePassword(Request $request): RedirectResponse
    {
        $user = $request->user();

        $data = $request->validate([
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user->password = Hash::make($data['password']);
        $user->save();

        return redirect()->route('guest.profile')->with('success', __('Password updated.'));
    }

    public function bookings(Request $request): View
    {
        $user = $request->user();
        if (! $user || ! $user->hasRole('Guest')) {
            abort(403);
        }
        $guest = optional($user)->guest;

        $bookings = collect();
        if ($guest) {
            $bookings = Booking::with('room')
                ->where('guest_id', $guest->id)
                ->orderByDesc('check_in_date')
                ->paginate(10);
        }

        return view('guest.bookings', compact('guest', 'bookings'));
    }
}

