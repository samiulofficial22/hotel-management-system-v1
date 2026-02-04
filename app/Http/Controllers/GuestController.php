<?php

namespace App\Http\Controllers;

use App\Models\Guest;
use App\Services\GuestService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class GuestController extends Controller
{
    public function __construct(
        protected GuestService $service
    ) {}

    public function index(Request $request): View
    {
        $guests = $this->service->paginateWithSearch(
            $request->integer('per_page', 15),
            $request->input('q')
        );
        return view('guests.index', compact('guests'));
    }

    public function create(): View
    {
        return view('guests.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $rules = array_merge($this->service->rules(), [
            'nid_photo_front' => ['nullable', 'file', 'image', 'max:2048'],
            'nid_photo_back' => ['nullable', 'file', 'image', 'max:2048'],
        ]);
        $validated = $request->validate($rules);
        $photoFront = $request->file('nid_photo_front');
        $photoBack = $request->file('nid_photo_back');
        unset($validated['nid_photo_front'], $validated['nid_photo_back']);
        $guest = $this->service->create($validated);
        $this->storeNidPhotos($guest, $photoFront, $photoBack);
        return redirect()->route('guests.index')->with('success', __('Guest created.'));
    }

    public function show(Guest $guest): View
    {
        $guest->load(['bookings.room.roomType', 'invoices']);
        return view('guests.show', compact('guest'));
    }

    public function edit(Guest $guest): View
    {
        return view('guests.edit', compact('guest'));
    }

    public function update(Request $request, Guest $guest): RedirectResponse
    {
        $rules = array_merge($this->service->rules(), [
            'nid_photo_front' => ['nullable', 'file', 'image', 'max:2048'],
            'nid_photo_back' => ['nullable', 'file', 'image', 'max:2048'],
        ]);
        $validated = $request->validate($rules);
        $photoFront = $request->file('nid_photo_front');
        $photoBack = $request->file('nid_photo_back');
        unset($validated['nid_photo_front'], $validated['nid_photo_back']);
        $this->service->update($guest, $validated);
        $this->storeNidPhotos($guest, $photoFront, $photoBack);
        return redirect()->route('guests.show', $guest)->with('success', __('Guest updated.'));
    }

    protected function storeNidPhotos(Guest $guest, $photoFront, $photoBack): void
    {
        $dir = 'guest-nid/' . $guest->id;
        if ($photoFront) {
            if ($guest->nid_photo_front) {
                Storage::disk('public')->delete($guest->nid_photo_front);
            }
            $path = $photoFront->store($dir, 'public');
            $guest->update(['nid_photo_front' => $path]);
        }
        if ($photoBack) {
            if ($guest->nid_photo_back) {
                Storage::disk('public')->delete($guest->nid_photo_back);
            }
            $path = $photoBack->store($dir, 'public');
            $guest->update(['nid_photo_back' => $path]);
        }
    }

    public function destroy(Guest $guest): RedirectResponse
    {
        $this->service->delete($guest);
        return redirect()->route('guests.index')->with('success', __('Guest deleted.'));
    }
}
