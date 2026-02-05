<?php

namespace App\Http\Controllers;

use App\Models\StoreItem;
use App\Repositories\StoreItemRepository;
use App\Services\DepartmentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StoreController extends Controller
{
    public function __construct(
        protected StoreItemRepository $repository,
        protected DepartmentService $departmentService
    ) {}

    public function index(): View
    {
        $items = $this->repository->all(false);
        return view('store.index', compact('items'));
    }

    public function create(): View
    {
        $departments = $this->departmentService->all(true);
        return view('store.create', compact('departments'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate(['name' => 'required|string|max:100', 'quantity' => 'nullable|integer|min:0', 'department_id' => 'nullable|exists:departments,id']);
        $d = $request->only(['name', 'sku', 'category', 'unit', 'notes', 'department_id']);
        $d['quantity'] = (int) $request->input('quantity', 0);
        $d['reorder_level'] = (int) $request->input('reorder_level', 0);
        $d['department_id'] = $request->input('department_id') ?: null;
        $this->repository->create($d);
        return redirect()->route('store.index')->with('success', __('Item created.'));
    }

    public function edit(StoreItem $store_item): View
    {
        $departments = $this->departmentService->all(true);
        return view('store.edit', ['item' => $store_item, 'departments' => $departments]);
    }

    public function update(Request $request, StoreItem $store_item): RedirectResponse
    {
        $request->validate(['name' => 'required|string|max:100', 'quantity' => 'nullable|integer|min:0', 'department_id' => 'nullable|exists:departments,id']);
        $data = $request->only(['name', 'sku', 'category', 'quantity', 'unit', 'reorder_level', 'notes', 'is_active']);
        $data['department_id'] = $request->input('department_id') ?: null;
        $this->repository->update($store_item, $data);
        return redirect()->route('store.index')->with('success', 'Item updated.');
    }
}
