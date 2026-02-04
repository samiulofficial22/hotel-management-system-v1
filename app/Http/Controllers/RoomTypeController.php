<?php

namespace App\Http\Controllers;

use App\Models\RoomType;
use App\Services\RoomTypeService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Validation\ValidationException;

class RoomTypeController extends Controller
{
    public function __construct(
        protected RoomTypeService $service
    ) {}

    public function index(Request $request): View
    {
        $roomTypes = $this->service->paginate($request->integer('per_page', 15));
        return view('room-types.index', compact('roomTypes'));
    }

    public function create(): View
    {
        return view('room-types.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate($this->service->rules());
        try {
            $this->service->create($validated);
            return redirect()->route('room-types.index')->with('success', __('Room type created.'));
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        }
    }

    public function edit(RoomType $roomType): View
    {
        return view('room-types.edit', compact('roomType'));
    }

    public function update(Request $request, RoomType $roomType): RedirectResponse
    {
        $rules = $this->service->rules(true);
        $rules['slug'] = ['nullable', 'string', 'max:100', 'unique:room_types,slug,' . $roomType->id];
        $validated = $request->validate($rules);
        try {
            $this->service->update($roomType, $validated);
            return redirect()->route('room-types.index')->with('success', __('Room type updated.'));
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        }
    }

    public function destroy(RoomType $roomType): RedirectResponse
    {
        $this->service->delete($roomType);
        return redirect()->route('room-types.index')->with('success', __('Room type deleted.'));
    }
}
