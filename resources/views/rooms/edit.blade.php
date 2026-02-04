@extends('layouts.app')
@section('title', 'Edit Room')
@section('content')
<h1 class="h3 mb-4">Edit Room</h1>
<form action="{{ route('rooms.update', $room) }}" method="POST">
    @csrf
    @method('PUT')
    <div class="row g-3">
        <div class="col-md-6"><label class="form-label">Room Type</label><select name="room_type_id" class="form-select" required>@foreach($roomTypes as $rt)<option value="{{ $rt->id }}" {{ old('room_type_id', $room->room_type_id) == $rt->id ? 'selected' : '' }}>{{ $rt->name }}</option>@endforeach</select></div>
        <div class="col-md-6"><label class="form-label">Room Number</label><input type="text" name="number" class="form-control" value="{{ old('number', $room->number) }}" required></div>
        <div class="col-md-6"><label class="form-label">Floor</label><input type="text" name="floor" class="form-control" value="{{ old('floor', $room->floor) }}"></div>
        <div class="col-md-6"><label class="form-label">Status</label><select name="status" class="form-select">@foreach([\App\Models\Room::STATUS_AVAILABLE, \App\Models\Room::STATUS_OCCUPIED, \App\Models\Room::STATUS_CLEANING, \App\Models\Room::STATUS_MAINTENANCE, \App\Models\Room::STATUS_OUT_OF_ORDER] as $s)@php $cfg = \App\Models\Room::statusBadgeConfig($s); @endphp<option value="{{ $s }}" {{ old('status', $room->status) == $s ? 'selected' : '' }}>{{ $cfg['label'] }}</option>@endforeach</select></div>
        <div class="col-12"><label class="form-label">Notes</label><textarea name="notes" class="form-control" rows="2">{{ old('notes', $room->notes) }}</textarea></div>
        <div class="col-12"><div class="form-check"><input type="checkbox" name="is_active" value="1" class="form-check-input" {{ $room->is_active ? 'checked' : '' }}><label class="form-check-label">Active</label></div></div>
        <div class="col-12"><button type="submit" class="btn btn-primary">Update</button> <a href="{{ route('rooms.index') }}" class="btn btn-secondary">Cancel</a></div>
    </div>
</form>
@endsection
