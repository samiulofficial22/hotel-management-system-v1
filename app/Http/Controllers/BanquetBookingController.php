<?php

namespace App\Http\Controllers;

use App\Models\BanquetBooking;
use App\Services\BanquetBookingService;
use App\Services\BanquetVenueService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Validation\ValidationException;

class BanquetBookingController extends Controller
{
    public function __construct(
        protected BanquetBookingService $service,
        protected BanquetVenueService $venueService
    ) {}

    public function index(Request $request): View
    {
        $bookings = $this->service->paginateWithSearch(
            $request->integer('per_page', 15),
            $request->input('q'),
            $request->filled('status') ? $request->status : null
        );
        return view('banquet.bookings.index', compact('bookings'));
    }

    public function create(): View
    {
        $venues = $this->venueService->all(true);
        return view('banquet.bookings.create', compact('venues'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate($this->service->rules());
        try {
            $booking = $this->service->create($validated, auth()->id());
            return redirect()->route('banquet.bookings.show', $booking)->with('success', __('Banquet booking created.'));
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        }
    }

    public function show(BanquetBooking $booking): View
    {
        $booking->load('banquetVenue');
        return view('banquet.bookings.show', compact('booking'));
    }

    public function edit(BanquetBooking $booking): View
    {
        $venues = $this->venueService->all(true);
        return view('banquet.bookings.edit', compact('booking', 'venues'));
    }

    public function update(Request $request, BanquetBooking $booking): RedirectResponse
    {
        $validated = $request->validate($this->service->rules(true));
        try {
            $this->service->update($booking, $validated);
            return redirect()->route('banquet.bookings.show', $booking)->with('success', __('Banquet booking updated.'));
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        }
    }
}
