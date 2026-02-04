@extends('layouts.app')
@section('title', 'Edit Booking')
@section('content')
<h1 class="h3 mb-4">Edit Booking</h1>
<form action="{{ route('bookings.update', $booking) }}" method="POST">
    @csrf
    @method('PUT')
    <div class="row g-3">
        <div class="col-md-6"><label class="form-label">Guest</label><select name="guest_id" class="form-select" required>@foreach($guests as $g)<option value="{{ $g->id }}" {{ old('guest_id', $booking->guest_id) == $g->id ? 'selected' : '' }}>{{ $g->full_name }}</option>@endforeach</select></div>
        <div class="col-md-6"><label class="form-label">Room</label><select name="room_id" class="form-select" required>@foreach($rooms as $r)<option value="{{ $r->id }}" {{ old('room_id', $booking->room_id) == $r->id ? 'selected' : '' }}>{{ $r->number }}</option>@endforeach</select></div>
        <div class="col-md-6"><label class="form-label">Check-in Date</label><input type="date" name="check_in_date" class="form-control" value="{{ old('check_in_date', $booking->check_in_date->format('Y-m-d')) }}" required></div>
        <div class="col-md-6"><label class="form-label">Check-out Date</label><input type="date" name="check_out_date" class="form-control" value="{{ old('check_out_date', $booking->check_out_date->format('Y-m-d')) }}" required></div>
        <div class="col-md-6"><label class="form-label">Room Rate</label><input type="number" step="0.01" name="room_rate" class="form-control" value="{{ old('room_rate', $booking->room_rate) }}" required></div>
        <div class="col-12"><button type="submit" class="btn btn-primary">Update</button> <a href="{{ route('bookings.show', $booking) }}" class="btn btn-secondary">Cancel</a></div>
    </div>
</form>
@endsection
