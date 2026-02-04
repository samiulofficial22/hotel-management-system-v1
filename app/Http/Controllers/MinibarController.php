<?php

namespace App\Http\Controllers;

use App\Models\MinibarItem;
use App\Repositories\MinibarItemRepository;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MinibarController extends Controller
{
    public function __construct(protected MinibarItemRepository $repository) {}

    public function index(): View
    {
        $items = $this->repository->all(false);
        return view('minibar.index', compact('items'));
    }

    public function create(): View
    {
        return view('minibar.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'sku' => 'nullable|string|max:50',
            'price' => 'required|numeric|min:0',
            'quantity_in_stock' => 'nullable|integer|min:0',
            'reorder_level' => 'nullable|integer|min:0',
        ]);
        $this->repository->create($request->only(['name', 'sku', 'price', 'quantity_in_stock', 'reorder_level']) + ['quantity_in_stock' => $request->input('quantity_in_stock', 0), 'reorder_level' => $request->input('reorder_level', 10)]);
        return redirect()->route('minibar.index')->with('success', __('Item created.'));
    }

    public function edit(MinibarItem $minibar_item): View
    {
        return view('minibar.edit', ['item' => $minibar_item]);
    }

    public function update(Request $request, MinibarItem $minibar_item): RedirectResponse
    {
        $request->validate(['name' => 'required|string|max:100', 'price' => 'required|numeric|min:0', 'quantity_in_stock' => 'nullable|integer|min:0', 'reorder_level' => 'nullable|integer|min:0']);
        $this->repository->update($minibar_item, $request->only(['name', 'sku', 'price', 'quantity_in_stock', 'reorder_level', 'is_active']));
        return redirect()->route('minibar.index')->with('success', __('Item updated.'));
    }
}
