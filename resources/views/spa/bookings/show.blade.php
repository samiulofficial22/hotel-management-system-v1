@extends('layouts.app')
@section('title', 'Spa Booking #' . $booking->id)
@section('content')
<div class="mb-4 d-flex justify-content-between align-items-center">
    <div>
        <a href="{{ route('spa.bookings.index') }}" class="btn btn-link text-decoration-none p-0">
            <i class="bi bi-arrow-left"></i> Back to Bookings
        </a>
        <h1 class="h3 mb-0 mt-2">Spa Booking #{{ $booking->id }}</h1>
    </div>
    <div class="dropdown">
        <button class="btn btn-outline-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
            Update Status
        </button>
        <ul class="dropdown-menu dropdown-menu-end">
            <li>
                <form action="{{ route('spa.bookings.update-status', $booking) }}" method="POST">
                    @csrf
                    <button type="submit" name="status" value="confirmed" class="dropdown-item">Confirmed</button>
                    <button type="submit" name="status" value="completed" class="dropdown-item text-success fw-bold">Completed</button>
                    <button type="submit" name="status" value="cancelled" class="dropdown-item text-danger">Cancelled</button>
                </form>
            </li>
        </ul>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <div class="row g-4">
            <div class="col-md-6 border-end">
                <h5 class="mb-3 border-bottom pb-2">Service Details</h5>
                <div class="mb-3">
                    <label class="text-muted small d-block">Service Name</label>
                    <span class="h5 mb-0">{{ $booking->service->name }}</span>
                </div>
                <div class="mb-3">
                    <label class="text-muted small d-block">Duration</label>
                    <span class="h5 mb-0">{{ $booking->service->duration_minutes }} Minutes</span>
                </div>
                <div class="mb-3">
                    <label class="text-muted small d-block">Date & Time</label>
                    <span class="h5 mb-0">{{ $booking->booking_date->format('d M, Y') }} at {{ date('h:i A', strtotime($booking->booking_time)) }}</span>
                </div>
                <div class="mb-0">
                    <label class="text-muted small d-block">Total Amount</label>
                    <span class="h4 mb-0 text-primary fw-bold">{{ money($booking->amount) }}</span>
                </div>
            </div>
            <div class="col-md-6">
                <h5 class="mb-3 border-bottom pb-2">Guest & Room</h5>
                <div class="mb-3">
                    <label class="text-muted small d-block">Guest Name</label>
                    <span class="h6 mb-0">{{ $booking->guest->name ?? 'N/A' }}</span>
                </div>
                <div class="mb-3">
                    <label class="text-muted small d-block">Room Number</label>
                    <span class="h6 mb-0">{{ $booking->room->number ?? 'N/A' }}</span>
                </div>
                <div class="mb-3">
                    <label class="text-muted small d-block">Status</label>
                    <span class="badge bg-primary">{{ ucfirst($booking->status) }}</span>
                    <span class="badge {{ $booking->payment_status === 'paid' ? 'bg-success' : 'bg-danger' }}">{{ ucfirst($booking->payment_status) }}</span>
                </div>
                <div class="mb-0">
                    <label class="text-muted small d-block">Payment Method</label>
                    <span class="text-uppercase small fw-bold">{{ $booking->payment_method ?? 'N/A' }}</span>
                </div>
            </div>
        </div>
        @if($booking->notes)
        <div class="mt-4 pt-3 border-top">
            <label class="text-muted small d-block">Notes</label>
            <p class="mb-0">{{ $booking->notes }}</p>
        </div>
        @endif
    </div>
</div>
@endsection
