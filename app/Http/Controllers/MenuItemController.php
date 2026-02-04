<?php

namespace App\Http\Controllers;

use App\Models\Outlet;
use App\Models\MenuCategory;
use App\Models\MenuItem;
use App\Services\MenuItemService;
use App\Services\MenuCategoryService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Validation\ValidationException;

class MenuItemController extends Controller
{
    public function __construct(
        protected MenuItemService $service,
        protected MenuCategoryService $categoryService
    ) {}

    public function index(Outlet $outlet): View
    {
        $categories = $this->categoryService->byOutlet($outlet->id, false);
        $items = $this->service->byOutlet($outlet->id, false);
        return view('menu.items.index', compact('outlet', 'categories', 'items'));
    }

    public function create(Outlet $outlet): View
    {
        $categories = $this->categoryService->byOutlet($outlet->id, false);
        return view('menu.items.create', compact('outlet', 'categories'));
    }

    public function store(Request $request, Outlet $outlet): RedirectResponse
    {
        $validated = $request->validate($this->service->rules());
        try {
            $this->service->create($validated);
            return redirect()->route('menu.items.index', $outlet)->with('success', __('Menu item created.'));
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        }
    }

    public function edit(Outlet $outlet, MenuItem $item): View
    {
        $categories = $this->categoryService->byOutlet($outlet->id, false);
        return view('menu.items.edit', compact('outlet', 'item', 'categories'));
    }

    public function update(Request $request, Outlet $outlet, MenuItem $item): RedirectResponse
    {
        $validated = $request->validate($this->service->rules(true));
        try {
            $this->service->update($item, $validated);
            return redirect()->route('menu.items.index', $outlet)->with('success', __('Menu item updated.'));
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        }
    }
}
