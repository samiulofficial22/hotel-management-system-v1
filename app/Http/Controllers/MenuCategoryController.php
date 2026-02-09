<?php

namespace App\Http\Controllers;

use App\Models\Outlet;
use App\Models\MenuCategory;
use App\Services\MenuCategoryService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Validation\ValidationException;

class MenuCategoryController extends Controller
{
    public function __construct(protected MenuCategoryService $service) {}

    public function index(Outlet $outlet): View
    {
        $categories = $this->service->byOutlet($outlet->id, false);
        return view('menu.categories.index', compact('outlet', 'categories'));
    }

    public function create(Outlet $outlet): View
    {
        return view('menu.categories.create', compact('outlet'));
    }

    public function store(Request $request, Outlet $outlet): RedirectResponse
    {
        $rules = $this->service->rules();
        unset($rules['outlet_id']);
        $validated = $request->validate($rules);
        $validated['outlet_id'] = $outlet->id;
        try {
            $this->service->create($validated);
            return redirect()->route('menu.categories.index', $outlet)->with('success', __('Category created.'));
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        }
    }

    public function edit(Outlet $outlet, MenuCategory $category): View
    {
        return view('menu.categories.edit', compact('outlet', 'category'));
    }

    public function update(Request $request, Outlet $outlet, MenuCategory $category): RedirectResponse
    {
        $validated = $request->validate($this->service->rules());
        try {
            $this->service->update($category, $validated);
            return redirect()->route('menu.categories.index', $outlet)->with('success', __('Category updated.'));
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        }
    }

    public function destroy(Outlet $outlet, MenuCategory $category): RedirectResponse
    {
        if ($category->outlet_id !== $outlet->id) {
            abort(404);
        }
        try {
            $this->service->delete($category);
            return redirect()->route('menu.categories.index', $outlet)->with('success', __('Category deleted.'));
        } catch (ValidationException $e) {
            return redirect()->route('menu.categories.index', $outlet)->withErrors($e->errors());
        }
    }
}
