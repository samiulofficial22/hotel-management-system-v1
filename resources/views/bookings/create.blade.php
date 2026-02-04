@extends('layouts.app')
@section('title', 'New Booking')
@section('content')
<h1 class="h3 mb-4">New Booking</h1>
<form action="{{ route('bookings.store') }}" method="POST">
    @csrf
    <div class="mb-3"><label class="form-label">Guest</label><select name="guest_id" class="form-select" required>@foreach($guests as $g)<option value="{{ $g->id }}">{{ $g->full_name }}</option>@endforeach</select></div>
    <div class="mb-3"><label class="form-label">Room</label><select name="room_id" class="form-select" required>@foreach($rooms as $r)<option value="{{ $r->id }}">{{ $r->number }} ({{ $r->roomType->name ?? '' }})</option>@endforeach</select></div>
    <div class="mb-3"><label class="form-label">Check-in Date</label><input type="date" name="check_in_date" class="form-control" value="{{ old('check_in_date') }}" required></div>
    <div class="mb-3"><label class="form-label">Check-out Date</label><input type="date" name="check_out_date" class="form-control" value="{{ old('check_out_date') }}" required></div>
    <div class="mb-3"><label class="form-label">Room Rate</label><input type="number" step="0.01" name="room_rate" class="form-control" value="{{ old('room_rate') }}" required></div>
    <div class="mb-3"><label class="form-label">Booking Type</label><select name="booking_type" class="form-select"><option value="advance">Advance</option><option value="walk_in">Walk-in</option></select></div>
    <button type="submit" class="btn btn-primary">Create Booking</button>
    <a href="{{ route('bookings.index') }}" class="btn btn-secondary">Cancel</a>
</form>
@endsection
