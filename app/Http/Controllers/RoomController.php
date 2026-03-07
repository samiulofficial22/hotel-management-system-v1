<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Services\RoomService;
use App\Services\RoomTypeService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Validation\ValidationException;

class RoomController extends Controller
{
    public function __construct(protected
        RoomService $service, protected
        RoomTypeService $roomTypeService
        )
    {
    }

    public function index(Request $request): View
    {
        $rooms = $this->service->paginateWithFilters(
            $request->integer('per_page', 15),
            $request->input('q'),
            $request->filled('room_type_id') ? (int)$request->room_type_id : null,
            $request->filled('status') ? $request->status : null
        );
        $roomTypes = $this->roomTypeService->all(true);
        return view('rooms.index', compact('rooms', 'roomTypes'));
    }

    public function create(): View
    {
        $roomTypes = $this->roomTypeService->all(true);
        return view('rooms.create', compact('roomTypes'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate($this->service->rules());
        try {
            $this->service->create($validated);
            return redirect()->route('rooms.index')->with('success', __('Room created.'));
        }
        catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        }
    }

    public function edit(Room $room): View
    {
        $roomTypes = $this->roomTypeService->all(true);
        return view('rooms.edit', compact('room', 'roomTypes'));
    }

    public function update(Request $request, Room $room): RedirectResponse
    {
        $rules = $this->service->rules(true);
        $rules['number'] = ['required', 'string', 'max:20', 'unique:rooms,number,' . $room->id];
        $validated = $request->validate($rules);
        try {
            $this->service->update($room, $validated);
            return redirect()->route('rooms.index')->with('success', __('Room updated.'));
        }
        catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        }
    }

    public function destroy(Room $room): RedirectResponse
    {
        $this->service->delete($room);
        return redirect()->route('rooms.index')->with('success', __('Room deleted.'));
    }

    public function apiAvailable(Request $request)
    {
        $checkIn = $request->input('check_in_date');
        $checkOut = $request->input('check_out_date');

        if (!$checkIn || !$checkOut) {
            return response()->json([]);
        }

        try {
            $checkInDate = \Carbon\Carbon::parse($checkIn);
            $checkOutDate = \Carbon\Carbon::parse($checkOut);
        }
        catch (\Exception $e) {
            return response()->json([]);
        }

        // Fetch rooms that are active and not booked in the given date range
        $rooms = Room::where('is_active', true)
            ->whereNotIn('id', function ($q) use ($checkInDate, $checkOutDate) {
            $q->select('room_id')
                ->from('bookings')
                ->whereNotIn('status', [\App\Models\Booking::STATUS_CANCELLED, \App\Models\Booking::STATUS_NO_SHOW])
                ->where('check_in_date', '<', $checkOutDate->toDateString())
                ->where('check_out_date', '>', $checkInDate->toDateString());
        })
            ->with('roomType')
            ->get();

        $results = $rooms->map(function ($r) {
            return [
            'id' => $r->id,
            'number' => $r->number,
            'type' => $r->roomType->name ?? '',
            'base_rate' => $r->roomType->base_rate ?? 0,
            'formatted_rate' => money($r->roomType->base_rate ?? 0)
            ];
        });

        return response()->json($results);
    }
}
