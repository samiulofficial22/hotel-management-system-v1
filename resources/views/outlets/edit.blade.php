@extends('layouts.app')
@section('title', 'Edit Outlet')
@section('content')
<h1 class="h3 mb-4">Edit Outlet</h1>
<form method="POST" action="{{ route('outlets.update', $outlet) }}">
@csrf
@method('PUT')
<div class="mb-3">
    <label class="form-label">Name</label>
    <input type="text" name="name" class="form-control" value="{{ old('name', $outlet->name) }}" required>
    @error('name')<div class="text-danger small">{{ $message }}</div>@enderror
</div>
<div class="mb-3">
    <label class="form-label">Type</label>
    <select name="type" class="form-select" required>
        <option value="restaurant" {{ old('type', $outlet->type) == 'restaurant' ? 'selected' : '' }}>Restaurant</option>
        <option value="cafe" {{ old('type', $outlet->type) == 'cafe' ? 'selected' : '' }}>Cafe</option>
        <option value="bar" {{ old('type', $outlet->type) == 'bar' ? 'selected' : '' }}>Bar</option>
    </select>
</div>
<div class="mb-3">
    <label class="form-label">Code (optional)</label>
    <input type="text" name="code" class="form-control" value="{{ old('code', $outlet->code) }}">
    @error('code')<div class="text-danger small">{{ $message }}</div>@enderror
</div>
<div class="mb-3 form-check">
    <input type="checkbox" name="is_active" value="1" class="form-check-input" {{ old('is_active', $outlet->is_active) ? 'checked' : '' }}>
    <label class="form-check-label">Active</label>
</div>
<button type="submit" class="btn btn-primary">Update</button>
<a href="{{ route('outlets.index') }}" class="btn btn-secondary">Cancel</a>
<hr class="my-4">
<form action="{{ route('outlets.destroy', $outlet) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('Delete this outlet? This cannot be undone.') }}');">
    @csrf
    @method('DELETE')
    <button type="submit" class="btn btn-outline-danger">Delete Outlet</button>
</form>
@endsection
