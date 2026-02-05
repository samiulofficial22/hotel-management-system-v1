<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Guest;
use App\Models\User;
use App\Services\BookingService;
use App\Services\NotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class GuestApprovalController extends Controller
{
    public function __construct(
        protected BookingService $bookingService,
        protected NotificationService $notificationService
    ) {}

    /** Pending guest booking requests list (admin / guest.manage). */
    public function index(Request $request): View
    {
        $bookings = Booking::with(['guest', 'room'])
            ->where('status', Booking::STATUS_PENDING)
            ->orderByDesc('created_at')
            ->paginate($request->integer('per_page', 15));

        return view('guest.requests.index', compact('bookings'));
    }

    /** Approve a pending request: create/link user, set role guest, confirm booking. */
    public function approve(Request $request, Booking $booking): RedirectResponse
    {
        if ($booking->status !== Booking::STATUS_PENDING) {
            return redirect()->route('guest.requests.index')
                ->with('success', __('This booking is no longer pending.'));
        }

        /** @var Guest|null $guest */
        $guest = $booking->guest;
        if (!$guest) {
            return redirect()->route('guest.requests.index')
                ->with('success', __('Guest not found for this booking.'));
        }

        // Create or reuse linked portal user for this guest.
        $user = $guest->user;
        if (!$user) {
            $email = $guest->email ?: 'guest+' . $guest->id . '@example.invalid';
            $password = Str::random(12);

            $user = User::create([
                'name' => $guest->full_name,
                'email' => $email,
                'password' => $password, // hashed by casts
            ]);
            $user->assignRole('Guest');

            $guest->user_id = $user->id;
            $guest->save();
        }

        try {
            $booking = $this->bookingService->approvePending($booking);
        } catch (ValidationException $e) {
            return redirect()->route('guest.requests.index')
                ->withErrors($e->errors());
        }

        $booking->load(['guest', 'room']);
        $this->notificationService->sendBookingConfirmation($booking);

        return redirect()->route('guest.requests.index')
            ->with('success', __('Booking approved and guest portal access enabled.'));
    }

    /** Reject a pending request: mark as cancelled, no user created. */
    public function reject(Request $request, Booking $booking): RedirectResponse
    {
        if ($booking->status !== Booking::STATUS_PENDING) {
            return redirect()->route('guest.requests.index')
                ->with('success', __('This booking is no longer pending.'));
        }

        $booking->update([
            'status' => Booking::STATUS_CANCELLED,
        ]);

        return redirect()->route('guest.requests.index')
            ->with('success', __('Booking request rejected.'));
    }
}

