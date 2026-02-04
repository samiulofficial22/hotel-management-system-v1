@extends('layouts.app')
@section('title', 'Add Banquet Venue')
@section('content')
<h1 class="h3 mb-4">Add Banquet Venue</h1>
<form method="POST" action="{{ route('banquet.venues.store') }}">
@csrf
<div class="mb-3">
    <label class="form-label">Name</label>
    <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
    @error('name')<div class="text-danger small">{{ $message }}</div>@enderror
</div>
<div class="mb-3">
    <label class="form-label">Capacity</label>
    <input type="number" name="capacity" class="form-control" min="1" value="{{ old('capacity') }}" required>
</div>
<div class="mb-3">
    <label class="form-label">Hourly rate (optional)</label>
    <input type="number" name="hourly_rate" class="form-control" step="0.01" min="0" value="{{ old('hourly_rate') }}">
</div>
<div class="mb-3">
    <label class="form-label">Fixed rate (optional)</label>
    <input type="number" name="fixed_rate" class="form-control" step="0.01" min="0" value="{{ old('fixed_rate') }}">
</div>
<div class="mb-3 form-check">
    <input type="checkbox" name="is_active" value="1" class="form-check-input" checked>
    <label class="form-check-label">Active</label>
</div>
<button type="submit" class="btn btn-primary">Create</button>
<a href="{{ route('banquet.venues.index') }}" class="btn btn-secondary">Cancel</a>
</form>
@endsection
