@extends('layouts.app')
@section('title', 'Add Category - ' . $outlet->name)
@section('content')
<h1 class="h3 mb-4">Add Menu Category: {{ $outlet->name }}</h1>
<form method="POST" action="{{ route('menu.categories.store', $outlet) }}">
@csrf
<div class="mb-3">
    <label class="form-label">Name</label>
    <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
    @error('name')<div class="text-danger small">{{ $message }}</div>@enderror
</div>
<div class="mb-3">
    <label class="form-label">Sort order</label>
    <input type="number" name="sort_order" class="form-control" value="{{ old('sort_order', 0) }}" min="0">
</div>
<div class="mb-3 form-check">
    <input type="checkbox" name="is_active" value="1" class="form-check-input" {{ old('is_active', true) ? 'checked' : '' }}>
    <label class="form-check-label">Active</label>
</div>
<button type="submit" class="btn btn-primary">Create</button>
<a href="{{ route('menu.categories.index', $outlet) }}" class="btn btn-secondary">Cancel</a>
</form>
@endsection
