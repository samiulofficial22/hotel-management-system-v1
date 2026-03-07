<?php

namespace App\Http\Controllers;

use App\Repositories\RoomRefreshmentRepository;
use Illuminate\Http\Request;

class RoomRefreshmentItemController extends Controller
{
    public function __construct(protected RoomRefreshmentRepository $repository)
    {
    }

    public function index()
    {
        $items = $this->repository->paginateItems();
        return view('refreshments.items.index', compact('items'));
    }

    public function create()
    {
        return view('refreshments.items.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'nullable|string|max:255',
            'price' => 'required|numeric|min:0',
            'cost_price' => 'required|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
            'is_active' => 'required|boolean'
        ]);

        $this->repository->createItem($data);
        return redirect()->route('refreshments.items.index')->with('success', 'Item created successfully');
    }

    public function edit(int $id)
    {
        $item = $this->repository->findItem($id);
        return view('refreshments.items.edit', compact('item'));
    }

    public function update(Request $request, int $id)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'nullable|string|max:255',
            'price' => 'required|numeric|min:0',
            'cost_price' => 'required|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
            'is_active' => 'required|boolean'
        ]);

        $this->repository->updateItem($id, $data);
        return redirect()->route('refreshments.items.index')->with('success', 'Item updated successfully');
    }

    public function destroy(int $id)
    {
        $this->repository->deleteItem($id);
        return redirect()->route('refreshments.items.index')->with('success', 'Item deleted successfully');
    }
}
