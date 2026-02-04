@extends('layouts.app')
@section('title', 'Edit Category')
@section('content')
<h1 class="h3 mb-4">Edit Category: {{ $category->name }}</h1>
<form method="POST" action="{{ route('menu.categories.update', [$outlet, $category]) }}">
@csrf
@method('PUT')
<div class="mb-3">
    <label class="form-label">Name</label>
    <input type="text" name="name" class="form-control" value="{{ old('name', $category->name) }}" required>
</div>
<div class="mb-3">
    <label class="form-label">Sort order</label>
    <input type="number" name="sort_order" class="form-control" value="{{ old('sort_order', $category->sort_order) }}" min="0">
</div>
<div class="mb-3 form-check">
    <input type="checkbox" name="is_active" value="1" class="form-check-input" {{ old('is_active', $category->is_active) ? 'checked' : '' }}>
    <label class="form-check-label">Active</label>
</div>
<button type="submit" class="btn btn-primary">Update</button>
<a href="{{ route('menu.categories.index', $outlet) }}" class="btn btn-secondary">Cancel</a>
</form>
@endsection
