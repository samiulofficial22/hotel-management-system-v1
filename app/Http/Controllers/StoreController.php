<?php

namespace App\Http\Controllers;

use App\Models\StoreItem;
use App\Repositories\StoreItemRepository;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StoreController extends Controller
{
    public function __construct(protected StoreItemRepository $repository) {}

    public function index(): View
    {
        $items = $this->repository->all(false);
        return view('store.index', compact('items'));
    }

    public function create(): View
    {
        return view('store.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate(['name' => 'required|string|max:100', 'quantity' => 'nullable|integer|min:0']);
        $d = $request->only(['name', 'sku', 'category', 'unit', 'notes']);
        $d['quantity'] = (int) $request->input('quantity', 0);
        $d['reorder_level'] = (int) $request->input('reorder_level', 0);
        $this->repository->create($d);
        return redirect()->route('store.index')->with('success', __('Item created.'));
    }

    public function edit(StoreItem $store_item): View
    {
        return view('store.edit', ['item' => $store_item]);
    }

    public function update(Request $request, StoreItem $store_item): RedirectResponse
    {
        $request->validate(['name' => 'required|string|max:100', 'quantity' => 'nullable|integer|min:0']);
        $this->repository->update($store_item, $request->only(['name', 'sku', 'category', 'quantity', 'unit', 'reorder_level', 'notes', 'is_active']));
        return redirect()->route('store.index')->with('success', 'Item updated.');
    }
}
