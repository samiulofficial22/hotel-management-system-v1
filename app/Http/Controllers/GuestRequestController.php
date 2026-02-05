<?php

namespace App\Http\Controllers;

use App\Models\Guest;
use App\Services\BookingService;
use App\Services\GuestService;
use App\Services\RoomService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class GuestRequestController extends Controller
{
    public function __construct(
        protected GuestService $guestService,
        protected BookingService $bookingService,
        protected RoomService $roomService
    ) {}

    /** Public booking request form (no login). */
    public function create(): View
    {
        $rooms = $this->roomService->all(true);
        return view('guest.request', compact('rooms'));
    }

    /** Handle booking request; create guest + pending booking. */
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'room_id' => ['required', 'exists:rooms,id'],
            'check_in_date' => ['required', 'date', 'after_or_equal:today'],
            'check_out_date' => ['required', 'date', 'after:check_in_date'],
            'adults' => ['nullable', 'integer', 'min:1', 'max:20'],
            'children' => ['nullable', 'integer', 'min:0', 'max:20'],
            'special_requests' => ['nullable', 'string'],
        ]);

        if (empty($data['email']) && empty($data['phone'])) {
            return back()
                ->withErrors(['email' => 'Please provide at least an email or a phone number.'])
                ->withInput();
        }

        // Find existing guest by email/phone or create new (no user account yet).
        $guestQuery = Guest::query();
        if (!empty($data['email'])) {
            $guestQuery->where('email', $data['email']);
        }
        if (!empty($data['phone'])) {
            $guestQuery->orWhere('phone', $data['phone']);
        }
        $guest = $guestQuery->first();

        if (!$guest) {
            $guest = $this->guestService->create([
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'email' => $data['email'] ?? null,
                'phone' => $data['phone'] ?? null,
                'nationality' => null,
                'id_type' => null,
                'id_number' => null,
                'date_of_birth' => null,
                'address' => null,
                'city' => null,
                'country' => null,
                'notes' => null,
            ]);
        }

        $bookingData = [
            'guest_id' => $guest->id,
            'room_id' => $data['room_id'],
            'check_in_date' => $data['check_in_date'],
            'check_out_date' => $data['check_out_date'],
            'adults' => $data['adults'] ?? 1,
            'children' => $data['children'] ?? 0,
            'room_rate' => 0,
            'special_requests' => $data['special_requests'] ?? null,
            'internal_notes' => trim('NEW – SAFE ADDITION: Created via website booking request.'),
        ];

        try {
            $this->bookingService->createPendingRequest($bookingData, null);
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        }

        return redirect()->route('booking.request')
            ->with('success', __('Thank you. Your booking request has been received and will be reviewed by our team.'));
    }
}

