<?php

namespace App\Http\Controllers;

use App\Models\BanquetVenue;
use App\Services\BanquetVenueService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Validation\ValidationException;

class BanquetVenueController extends Controller
{
    public function __construct(protected BanquetVenueService $service) {}

    public function index(): View
    {
        $venues = $this->service->all(false);
        return view('banquet.venues.index', compact('venues'));
    }

    public function create(): View
    {
        return view('banquet.venues.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate($this->service->rules());
        try {
            $this->service->create($validated);
            return redirect()->route('banquet.venues.index')->with('success', __('Venue created.'));
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        }
    }

    public function edit(BanquetVenue $venue): View
    {
        return view('banquet.venues.edit', compact('venue'));
    }

    public function update(Request $request, BanquetVenue $venue): RedirectResponse
    {
        $validated = $request->validate($this->service->rules(true));
        try {
            $this->service->update($venue, $validated);
            return redirect()->route('banquet.venues.index')->with('success', __('Venue updated.'));
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        }
    }
}
