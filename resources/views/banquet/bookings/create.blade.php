@extends('layouts.app')
@section('title', 'New Banquet Booking')
@section('content')
<h1 class="h3 mb-4">New Banquet Booking</h1>
<form method="POST" action="{{ route('banquet.bookings.store') }}">
@csrf
<div class="mb-3">
    <label class="form-label">Venue</label>
    <select name="banquet_venue_id" class="form-select" required>
        @foreach($venues as $v)
        <option value="{{ $v->id }}">{{ $v->name }}</option>
        @endforeach
    </select>
</div>
<div class="mb-3">
    <label class="form-label">Event name</label>
    <input type="text" name="event_name" class="form-control" required>
</div>
<div class="mb-3">
    <label class="form-label">Event date</label>
    <input type="date" name="event_date" class="form-control" required>
</div>
<div class="mb-3">
    <label class="form-label">Start / End time</label>
    <input type="time" name="start_time" class="form-control" required>
    <input type="time" name="end_time" class="form-control" required>
</div>
<div class="mb-3">
    <label class="form-label">Guest count</label>
    <input type="number" name="guest_count" class="form-control" min="1" required>
</div>
<div class="mb-3">
    <label class="form-label">Contact name</label>
    <input type="text" name="contact_name" class="form-control" required>
</div>
<div class="mb-3">
    <label class="form-label">Contact phone</label>
    <input type="text" name="contact_phone" class="form-control" required>
</div>
<button type="submit" class="btn btn-primary">Create</button>
<a href="{{ route('banquet.bookings.index') }}" class="btn btn-secondary">Cancel</a>
</form>
@endsection
