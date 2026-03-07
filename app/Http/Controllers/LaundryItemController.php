<?php

namespace App\Http\Controllers;

use App\Models\LaundryItem;
use App\Repositories\LaundryItemRepository;
use Illuminate\Http\Request;

class LaundryItemController extends Controller
{
    public function __construct(protected LaundryItemRepository $repository)
    {
    }

    public function index()
    {
        $items = $this->repository->all();
        return view('laundry.items.index', compact('items'));
    }

    public function create()
    {
        return view('laundry.items.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100',
            'wash_price' => 'required|numeric|min:0',
            'iron_price' => 'required|numeric|min:0',
            'dry_clean_price' => 'required|numeric|min:0',
            'is_active' => 'boolean'
        ]);

        $this->repository->create($data);
        return redirect()->route('laundry.items.index')->with('success', 'Item created successfully');
    }

    public function edit(LaundryItem $item)
    {
        return view('laundry.items.edit', compact('item'));
    }

    public function update(Request $request, LaundryItem $item)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100',
            'wash_price' => 'required|numeric|min:0',
            'iron_price' => 'required|numeric|min:0',
            'dry_clean_price' => 'required|numeric|min:0',
            'is_active' => 'boolean'
        ]);

        $this->repository->update($item, $data);
        return redirect()->route('laundry.items.index')->with('success', 'Item updated successfully');
    }

    public function destroy(LaundryItem $item)
    {
        $this->repository->delete($item);
        return redirect()->route('laundry.items.index')->with('success', 'Item deleted successfully');
    }
}
