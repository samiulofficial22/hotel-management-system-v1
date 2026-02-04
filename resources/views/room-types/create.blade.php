@extends('layouts.app')
@section('title', 'Add Room Type')
@section('content')
<h1 class="h3 mb-4">Add Room Type</h1>
<form action="{{ route('room-types.store') }}" method="POST">
    @csrf
    <div class="mb-3"><label class="form-label">Name</label><input type="text" name="name" class="form-control" value="{{ old('name') }}" required></div>
    <div class="mb-3"><label class="form-label">Slug (optional)</label><input type="text" name="slug" class="form-control" value="{{ old('slug') }}"></div>
    <div class="mb-3"><label class="form-label">Base Rate</label><input type="number" step="0.01" name="base_rate" class="form-control" value="{{ old('base_rate', 0) }}" required></div>
    <div class="mb-3"><label class="form-label">Max Occupancy</label><input type="number" name="max_occupancy" class="form-control" value="{{ old('max_occupancy', 2) }}" required></div>
    <div class="mb-3"><div class="form-check"><input type="checkbox" name="is_active" value="1" class="form-check-input" checked><label class="form-check-label">Active</label></div></div>
    <button type="submit" class="btn btn-primary">Save</button>
    <a href="{{ route('room-types.index') }}" class="btn btn-secondary">Cancel</a>
</form>
@endsection
