<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Services\BookingService;
use App\Services\GuestService;
use App\Services\RoomService;
use App\Services\InvoiceService;
use App\Services\NotificationService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Validation\ValidationException;

class BookingController extends Controller
{
    public function __construct(
        protected BookingService $service,
        protected GuestService $guestService,
        protected RoomService $roomService,
        protected InvoiceService $invoiceService,
        protected NotificationService $notificationService
    ) {}

    public function index(Request $request): View
    {
        $bookings = $this->service->paginateWithSearch(
            $request->integer('per_page', 15),
            $request->input('q'),
            $request->filled('status') ? $request->status : null
        );
        return view('bookings.index', compact('bookings'));
    }

    public function create(): View
    {
        $guests = $this->guestService->all();
        $rooms = $this->roomService->getAvailable();
        $roomTypes = $this->roomService->all(true)->groupBy('room_type_id');
        return view('bookings.create', compact('guests', 'rooms', 'roomTypes'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate($this->service->rules());
        try {
            $booking = $this->service->create($validated, auth()->id());
            $booking->load(['guest', 'room']);
            $this->notificationService->sendBookingConfirmation($booking);
            return redirect()->route('bookings.show', $booking)->with('success', __('Booking created.'));
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        }
    }

    public function show(Booking $booking): View
    {
        $booking->load(['guest', 'room.roomType', 'invoices.payments']);
        return view('bookings.show', compact('booking'));
    }

    public function edit(Booking $booking): View
    {
        $guests = $this->guestService->all();
        $rooms = $this->roomService->all(true);
        return view('bookings.edit', compact('booking', 'guests', 'rooms'));
    }

    public function update(Request $request, Booking $booking): RedirectResponse
    {
        $rules = $this->service->rules(true);
        $validated = $request->validate($rules);
        try {
            $this->service->update($booking, $validated);
            return redirect()->route('bookings.show', $booking)->with('success', __('Booking updated.'));
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        }
    }

    public function destroy(Booking $booking): RedirectResponse
    {
        $this->service->cancel($booking);
        return redirect()->route('bookings.index')->with('success', __('Booking cancelled.'));
    }

    /** Calendar view: bookings for date range. */
    public function calendar(Request $request): View
    {
        $start = Carbon::parse($request->input('start', now()->startOfMonth()));
        $end = Carbon::parse($request->input('end', now()->endOfMonth()));
        $bookings = $this->service->getForDateRange($start, $end);
        return view('bookings.calendar', compact('bookings', 'start', 'end'));
    }

    public function checkIn(Booking $booking): RedirectResponse
    {
        $this->service->checkIn($booking);
        return redirect()->route('bookings.show', $booking)->with('success', __('Guest checked in.'));
    }

    public function checkOut(Request $request, Booking $booking): RedirectResponse
    {
        $lateFee = (float) ($request->input('late_checkout_fee', 0));
        $this->service->checkOut($booking, $lateFee);
        $invoice = $this->invoiceService->createFromBooking($booking->fresh(), 0, 0);
        $booking->load(['guest', 'room']);
        $this->notificationService->sendCheckOutSummary($booking);
        return redirect()->route('bookings.show', $booking)->with('success', __('messages.Guest checked out. Invoice created.'));
    }
}
