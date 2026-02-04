@extends('layouts.app')
@section('title', 'Add Outlet')
@section('content')
<h1 class="h3 mb-4">Add Outlet</h1>
<form method="POST" action="{{ route('outlets.store') }}">
@csrf
<div class="mb-3">
    <label class="form-label">Name</label>
    <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
    @error('name')<div class="text-danger small">{{ $message }}</div>@enderror
</div>
<div class="mb-3">
    <label class="form-label">Type</label>
    <select name="type" class="form-select" required>
        <option value="restaurant" {{ old('type') === 'restaurant' ? 'selected' : '' }}>Restaurant</option>
        <option value="cafe" {{ old('type') === 'cafe' ? 'selected' : '' }}>Cafe</option>
        <option value="bar" {{ old('type') === 'bar' ? 'selected' : '' }}>Bar</option>
    </select>
</div>
<div class="mb-3">
    <label class="form-label">Code (optional)</label>
    <input type="text" name="code" class="form-control" value="{{ old('code') }}">
    @error('code')<div class="text-danger small">{{ $message }}</div>@enderror
</div>
<div class="mb-3 form-check">
    <input type="checkbox" name="is_active" value="1" class="form-check-input" {{ old('is_active', true) ? 'checked' : '' }}>
    <label class="form-check-label">Active</label>
</div>
<button type="submit" class="btn btn-primary">Create</button>
<a href="{{ route('outlets.index') }}" class="btn btn-secondary">Cancel</a>
</form>
@endsection
