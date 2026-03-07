<?php

namespace App\Http\Controllers;

use App\Models\SpaBooking;
use App\Models\Room;
use App\Models\SpaService as SpaServiceModel;
use App\Services\SpaBookingService;
use App\Repositories\SpaBookingRepository;
use Illuminate\Http\Request;

class SpaBookingController extends Controller
{
    public function __construct(protected
        SpaBookingRepository $repository, protected
        SpaBookingService $service
        )
    {
    }

    public function index(Request $request)
    {
        $bookings = $this->repository->paginate(15, $request->status);
        return view('spa.bookings.index', compact('bookings'));
    }

    public function create()
    {
        $rooms = Room::where('status', Room::STATUS_OCCUPIED)->get();
        $services = SpaServiceModel::where('is_active', true)->get();
        return view('spa.bookings.create', compact('rooms', 'services'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'spa_service_id' => 'required|exists:spa_services,id',
            'booking_date' => 'required|date',
            'booking_time' => 'required',
            'payment_method' => 'nullable|string',
            'payment_status' => 'required|in:unpaid,paid',
            'status' => 'required|in:pending,confirmed,completed',
            'notes' => 'nullable|string'
        ]);

        $room = Room::find($request->room_id);
        $spaService = SpaServiceModel::find($request->spa_service_id);

        $data['guest_id'] = $room->latestBooking->guest_id ?? null;
        $data['amount'] = $spaService->price;
        $data['created_by'] = auth()->id();

        $this->service->createBooking($data);

        return redirect()->route('spa.bookings.index')->with('success', 'Spa booking created');
    }

    public function show(SpaBooking $booking)
    {
        $booking->load(['service', 'room', 'guest', 'createdBy']);
        return view('spa.bookings.show', compact('booking'));
    }

    public function updateStatus(Request $request, SpaBooking $booking)
    {
        $request->validate(['status' => 'required|in:pending,confirmed,completed,cancelled']);
        $this->service->updateStatus($booking, $request->status);
        return back()->with('success', 'Status updated');
    }

    public function destroy(SpaBooking $booking)
    {
        $this->repository->delete($booking);
        return redirect()->route('spa.bookings.index')->with('success', 'Booking deleted');
    }
}
