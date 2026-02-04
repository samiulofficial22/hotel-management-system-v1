@extends('layouts.app')
@section('title', 'Edit Banquet Booking')
@section('content')
<h1 class="h3 mb-4">Edit Banquet Booking: {{ $booking->event_name }}</h1>
<form method="POST" action="{{ route('banquet.bookings.update', $booking) }}">
@csrf
@method('PUT')
<div class="row">
<div class="col-md-6">
<div class="mb-3">
    <label class="form-label">Venue</label>
    <select name="banquet_venue_id" class="form-select" required>
        @foreach($venues as $v)
        <option value="{{ $v->id }}" {{ old('banquet_venue_id', $booking->banquet_venue_id) == $v->id ? 'selected' : '' }}>{{ $v->name }} ({{ $v->capacity }} pax)</option>
        @endforeach
    </select>
</div>
<div class="mb-3">
    <label class="form-label">Event name</label>
    <input type="text" name="event_name" class="form-control" value="{{ old('event_name', $booking->event_name) }}" required>
</div>
<div class="mb-3">
    <label class="form-label">Event date</label>
    <input type="date" name="event_date" class="form-control" value="{{ old('event_date', $booking->event_date->format('Y-m-d')) }}" required>
</div>
<div class="row">
<div class="col-6"><div class="mb-3">
    <label class="form-label">Start time</label>
    <input type="time" name="start_time" class="form-control" value="{{ old('start_time', \Carbon\Carbon::parse($booking->start_time)->format('H:i')) }}" required>
</div></div>
<div class="col-6"><div class="mb-3">
    <label class="form-label">End time</label>
    <input type="time" name="end_time" class="form-control" value="{{ old('end_time', \Carbon\Carbon::parse($booking->end_time)->format('H:i')) }}" required>
</div></div>
</div>
<div class="mb-3">
    <label class="form-label">Guest count</label>
    <input type="number" name="guest_count" class="form-control" min="1" value="{{ old('guest_count', $booking->guest_count) }}" required>
</div>
<div class="mb-3">
    <label class="form-label">Package name (optional)</label>
    <input type="text" name="package_name" class="form-control" value="{{ old('package_name', $booking->package_name) }}">
</div>
<div class="mb-3">
    <label class="form-label">Total amount</label>
    <input type="number" name="total_amount" class="form-control" step="0.01" min="0" value="{{ old('total_amount', $booking->total_amount) }}">
</div>
<div class="mb-3">
    <label class="form-label">Status</label>
    <select name="status" class="form-select">
        <option value="pending" {{ old('status', $booking->status) == 'pending' ? 'selected' : '' }}>Pending</option>
        <option value="confirmed" {{ old('status', $booking->status) == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
        <option value="cancelled" {{ old('status', $booking->status) == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
        <option value="completed" {{ old('status', $booking->status) == 'completed' ? 'selected' : '' }}>Completed</option>
    </select>
</div>
</div>
<div class="col-md-6">
<div class="mb-3">
    <label class="form-label">Contact name</label>
    <input type="text" name="contact_name" class="form-control" value="{{ old('contact_name', $booking->contact_name) }}" required>
</div>
<div class="mb-3">
    <label class="form-label">Contact phone</label>
    <input type="text" name="contact_phone" class="form-control" value="{{ old('contact_phone', $booking->contact_phone) }}" required>
</div>
<div class="mb-3">
    <label class="form-label">Contact email (optional)</label>
    <input type="email" name="contact_email" class="form-control" value="{{ old('contact_email', $booking->contact_email) }}">
</div>
<div class="mb-3">
    <label class="form-label">Notes (optional)</label>
    <textarea name="notes" class="form-control" rows="3">{{ old('notes', $booking->notes) }}</textarea>
</div>
</div>
</div>
<button type="submit" class="btn btn-primary">Update</button>
<a href="{{ route('banquet.bookings.show', $booking) }}" class="btn btn-secondary">Cancel</a>
</form>
@endsection
