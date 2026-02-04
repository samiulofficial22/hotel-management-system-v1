@extends('layouts.app')
@section('title', 'Add Room')
@section('content')
<h1 class="h3 mb-4">Add Room</h1>
<form action="{{ route('rooms.store') }}" method="POST">
    @csrf
    <div class="mb-3"><label class="form-label">Room Type</label><select name="room_type_id" class="form-select" required>@foreach($roomTypes as $rt)<option value="{{ $rt->id }}">{{ $rt->name }}</option>@endforeach</select></div>
    <div class="mb-3"><label class="form-label">Room Number</label><input type="text" name="number" class="form-control" value="{{ old('number') }}" required></div>
    <div class="mb-3"><label class="form-label">Floor</label><input type="text" name="floor" class="form-control" value="{{ old('floor') }}"></div>
    <div class="mb-3"><label class="form-label">Status</label><select name="status" class="form-select"><option value="available">Available</option><option value="occupied">Occupied</option><option value="cleaning">Cleaning</option><option value="maintenance">Maintenance</option><option value="out_of_order">Out of Order</option></select></div>
    <div class="mb-3"><div class="form-check"><input type="checkbox" name="is_active" value="1" class="form-check-input" checked><label class="form-check-label">Active</label></div></div>
    <button type="submit" class="btn btn-primary">Save</button>
    <a href="{{ route('rooms.index') }}" class="btn btn-secondary">Cancel</a>
</form>
@endsection
