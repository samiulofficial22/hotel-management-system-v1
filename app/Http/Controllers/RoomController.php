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
    public function __construct(
        protected RoomService $service,
        protected RoomTypeService $roomTypeService
    ) {}

    public function index(Request $request): View
    {
        $rooms = $this->service->paginateWithFilters(
            $request->integer('per_page', 15),
            $request->input('q'),
            $request->filled('room_type_id') ? (int) $request->room_type_id : null,
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
        } catch (ValidationException $e) {
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
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        }
    }

    public function destroy(Room $room): RedirectResponse
    {
        $this->service->delete($room);
        return redirect()->route('rooms.index')->with('success', __('Room deleted.'));
    }
}
