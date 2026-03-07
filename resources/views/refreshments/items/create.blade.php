@extends('layouts.app')
@section('title', 'Add Refreshment Item')
@section('content')
<div class="mb-4">
    <a href="{{ route('refreshments.items.index') }}" class="btn btn-link text-decoration-none p-0">
        <i class="bi bi-arrow-left"></i> Back to Items
    </a>
    <h1 class="h3 mb-0 mt-2">Add Refreshment Item</h1>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <form action="{{ route('refreshments.items.store') }}" method="POST">
            @csrf
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Item Name</label>
                    <input type="text" name="name" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Category</label>
                    <input type="text" name="category" class="form-control">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Sale Price (৳)</label>
                    <input type="number" step="0.01" name="price" class="form-control" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Cost Price (৳)</label>
                    <input type="number" step="0.01" name="cost_price" class="form-control" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Initial Stock</label>
                    <input type="number" name="stock_quantity" class="form-control" value="0" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Active</label>
                    <select name="is_active" class="form-select">
                        <option value="1">Yes</option>
                        <option value="0">No</option>
                    </select>
                </div>
                <div class="col-12 text-end">
                    <button type="submit" class="btn btn-primary">Save Item</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
