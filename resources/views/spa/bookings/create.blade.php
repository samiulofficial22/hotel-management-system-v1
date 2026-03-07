@extends('layouts.app')
@section('title', 'Create Spa Booking')
@section('content')
<div class="mb-4">
    <a href="{{ route('spa.bookings.index') }}" class="btn btn-link text-decoration-none p-0">
        <i class="bi bi-arrow-left"></i> Back to Bookings
    </a>
    <h1 class="h3 mb-0 mt-2">New Spa Booking</h1>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <form action="{{ route('spa.bookings.store') }}" method="POST">
            @csrf
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Room</label>
                    <select name="room_id" class="form-select" required>
                        <option value="">-- Select Room --</option>
                        @foreach($rooms as $room)
                            <option value="{{ $room->id }}">{{ $room->number }} ({{ $room->latestBooking->guest->name ?? 'N/A' }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Service</label>
                    <select name="spa_service_id" class="form-select" required>
                        <option value="">-- Select Service --</option>
                        @foreach($services as $service)
                            <option value="{{ $service->id }}">{{ $service->name }} ({{ money($service->price) }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Booking Date</label>
                    <input type="date" name="booking_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Booking Time</label>
                    <input type="time" name="booking_time" class="form-control" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Payment Method</label>
                    <select name="payment_method" class="form-select">
                        <option value="cash">Cash</option>
                        <option value="bank">Bank/Card</option>
                        <option value="post_to_room">Post to Room Bill</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Payment Status</label>
                    <select name="payment_status" class="form-select">
                        <option value="unpaid">Unpaid</option>
                        <option value="paid">Paid</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="pending">Pending</option>
                        <option value="confirmed">Confirmed</option>
                    </select>
                </div>
                <div class="col-12">
                    <label class="form-label">Notes</label>
                    <textarea name="notes" class="form-control" rows="2"></textarea>
                </div>
                <div class="col-12 text-end">
                    <button type="submit" class="btn btn-primary px-4">Create Booking</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
