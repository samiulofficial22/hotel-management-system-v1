@extends('layouts.app')
@section('title', 'Edit Minibar Item')
@section('content')
<h1 class="h3 mb-4">Edit: {{ $item->name }}</h1>
<form method="POST" action="{{ route('minibar.update', $item) }}">
@csrf
@method('PUT')
<div class="mb-3"><label class="form-label">Name</label><input type="text" name="name" class="form-control" value="{{ old('name', $item->name) }}" required></div>
<div class="mb-3"><label class="form-label">Price</label><input type="number" name="price" class="form-control" step="0.01" min="0" value="{{ old('price', $item->price) }}" required></div>
<div class="mb-3"><label class="form-label">Quantity in stock</label><input type="number" name="quantity_in_stock" class="form-control" min="0" value="{{ old('quantity_in_stock', $item->quantity_in_stock) }}"></div>
<div class="mb-3 form-check"><input type="checkbox" name="is_active" value="1" class="form-check-input" {{ old('is_active', $item->is_active) ? 'checked' : '' }}><label class="form-check-label">Active</label></div>
<button type="submit" class="btn btn-primary">Update</button>
<a href="{{ route('minibar.index') }}" class="btn btn-secondary">Cancel</a>
</form>
@endsection
