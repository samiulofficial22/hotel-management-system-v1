<?php

namespace App\Http\Controllers;

use App\Models\Outlet;
use App\Services\OutletService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Validation\ValidationException;

class OutletController extends Controller
{
    public function __construct(protected OutletService $service)
    {
    }

    public function index(): View
    {
        $outlets = $this->service->all(false);
        return view('outlets.index', compact('outlets'));
    }

    public function create(): View
    {
        return view('outlets.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate($this->service->rules());
        try {
            $this->service->create($validated);
            return redirect()->route('outlets.index')->with('success', __('Outlet created.'));
        }
        catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        }
    }

    public function edit(Outlet $outlet): View
    {
        return view('outlets.edit', compact('outlet'));
    }

    public function update(Request $request, Outlet $outlet): RedirectResponse
    {
        $validated = $request->validate($this->service->rules($outlet->id));
        try {
            $this->service->update($outlet, $validated);
            return redirect()->route('outlets.index')->with('success', __('Outlet updated.'));
        }
        catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        }
    }

    public function destroy(Outlet $outlet): RedirectResponse
    {
        try {
            $this->service->delete($outlet);
            return redirect()->route('outlets.index')->with('success', __('Outlet deleted.'));
        }
        catch (ValidationException $e) {
            return redirect()->route('outlets.index')->withErrors($e->errors());
        }
    }
}
