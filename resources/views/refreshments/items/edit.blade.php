@extends('layouts.app')
@section('title', 'Edit Refreshment Item')
@section('content')
<div class="mb-4">
    <a href="{{ route('refreshments.items.index') }}" class="btn btn-link text-decoration-none p-0">
        <i class="bi bi-arrow-left"></i> Back to Items
    </a>
    <h1 class="h3 mb-0 mt-2">Edit: {{ $item->name }}</h1>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <form action="{{ route('refreshments.items.update', $item->id) }}" method="POST">
            @csrf @method('PUT')
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Item Name</label>
                    <input type="text" name="name" class="form-control" value="{{ $item->name }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Category</label>
                    <input type="text" name="category" class="form-control" value="{{ $item->category }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Sale Price (৳)</label>
                    <input type="number" step="0.01" name="price" class="form-control" value="{{ $item->price }}" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Cost Price (৳)</label>
                    <input type="number" step="0.01" name="cost_price" class="form-control" value="{{ $item->cost_price }}" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Stock Quantity</label>
                    <input type="number" name="stock_quantity" class="form-control" value="{{ $item->stock_quantity }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Active</label>
                    <select name="is_active" class="form-select">
                        <option value="1" {{ $item->is_active ? 'selected' : '' }}>Yes</option>
                        <option value="0" {{ !$item->is_active ? 'selected' : '' }}>No</option>
                    </select>
                </div>
                <div class="col-12 text-end">
                    <button type="submit" class="btn btn-primary">Update Item</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
