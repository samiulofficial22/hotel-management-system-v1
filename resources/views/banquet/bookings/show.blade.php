@extends('layouts.app')
@section('title', 'Booking Details')
@section('content')
<div class="mb-4">
    <a href="{{ route('banquet.bookings.index') }}" class="btn btn-light btn-sm text-muted">
        <i class="fas fa-arrow-left me-1"></i> Back to Bookings
    </a>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center border-bottom-0">
                <h1 class="h4 mb-0 text-dark">{{ $booking->event_name }}</h1>
                @php $cfg = \App\Models\BanquetBooking::statusBadgeConfig($booking->status); @endphp
                <span class="badge {{ $cfg['class'] }} rounded-pill px-3 py-2">
                    {{ $cfg['label'] }}
                </span>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-6 border-end">
                        <label class="small text-muted text-uppercase fw-bold mb-2 d-block">Venue Details</label>
                        <h5 class="mb-1 text-primary">{{ $booking->banquetVenue->name ?? 'N/A' }}</h5>
                        <p class="text-muted small mb-0">
                            <i class="fas fa-map-marker-alt me-1"></i> {{ $booking->banquetVenue->location ?? 'No location set' }}
                        </p>
                        <p class="text-muted small mb-0">
                            <i class="fas fa-users me-1"></i> Capacity: {{ $booking->banquetVenue->capacity ?? '0' }} persons
                        </p>
                    </div>
                    <div class="col-md-6 ps-md-4">
                        <label class="small text-muted text-uppercase fw-bold mb-2 d-block">Schedule</label>
                        <div class="d-flex align-items-center mb-2">
                            <div class="bg-light rounded p-2 me-3 text-center" style="min-width: 60px;">
                                <div class="text-primary fw-bold">{{ $booking->event_date->format('d') }}</div>
                                <div class="small text-muted text-uppercase">{{ $booking->event_date->format('M') }}</div>
                            </div>
                            <div>
                                <div class="fw-bold text-dark">{{ $booking->event_date->format('l, Y') }}</div>
                                <div class="small text-muted">
                                    {{ \Carbon\Carbon::parse($booking->start_time)->format('h:i A') }} - {{ \Carbon\Carbon::parse($booking->end_time)->format('h:i A') }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <hr class="opacity-25">

                <div class="row mb-4 mt-4">
                    <div class="col-md-4 mb-3">
                        <label class="small text-muted text-uppercase fw-bold d-block mb-1">Expected Guests</label>
                        <div class="h5 mb-0 text-dark"><i class="fas fa-user-friends me-2 opacity-50"></i>{{ $booking->guest_count }}</div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="small text-muted text-uppercase fw-bold d-block mb-1">Selected Package</label>
                        <div class="h5 mb-0 text-dark"><i class="fas fa-utensils me-2 opacity-50"></i>{{ $booking->package_name ?? 'Room Only' }}</div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="small text-muted text-uppercase fw-bold d-block mb-1">Total Amount</label>
                        <div class="h5 mb-0 text-success fw-bold">$ {{ number_format($booking->total_amount, 2) }}</div>
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-4 mb-3">
                        <label class="small text-muted text-uppercase fw-bold d-block mb-1">Payment Method</label>
                        <div class="h6 mb-0 text-dark"><i class="fas fa-money-bill-wave me-2 opacity-50"></i>{{ strtoupper($booking->payment_method ?? 'CASH') }}</div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="small text-muted text-uppercase fw-bold d-block mb-1">Payment Status</label>
                        <span class="badge {{ $booking->payment_status === 'paid' ? 'bg-success' : 'bg-danger' }} px-3 rounded-pill">
                            {{ strtoupper($booking->payment_status ?? 'unpaid') }}
                        </span>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="small text-muted text-uppercase fw-bold d-block mb-1">Total Duration</label>
                        <div class="h6 mb-0 text-dark"><i class="fas fa-clock me-2 opacity-50"></i>{{ $booking->duration_hours }} Hours</div>
                    </div>
                </div>

                @if($booking->notes)
                <div class="alert alert-light border-0">
                    <label class="small text-muted text-uppercase fw-bold d-block mb-2">Internal Notes</label>
                    <p class="mb-0 text-dark" style="white-space: pre-wrap;">{{ $booking->notes }}</p>
                </div>
                @endif
            </div>
            <div class="card-footer bg-white py-3 border-top-0 d-flex gap-2">
                <a href="{{ route('banquet.bookings.edit', $booking) }}" class="btn btn-primary px-4 shadow-sm">
                    <i class="fas fa-edit me-1"></i> Edit Booking
                </a>
                <form action="{{ route('banquet.bookings.destroy', $booking) }}" method="POST" onsubmit="return confirm('Archive this booking?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-outline-danger px-4">
                        <i class="fas fa-trash me-1"></i> Delete
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-bottom-0 py-3">
                <h5 class="mb-0 small fw-bold text-uppercase">Contact Information</h5>
            </div>
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <div class="bg-primary-subtle text-primary rounded-circle p-2 me-3">
                        <i class="fas fa-user fa-fw"></i>
                    </div>
                    <div>
                        <div class="small text-muted">Contact Name</div>
                        <div class="fw-bold text-dark">{{ $booking->contact_name }}</div>
                    </div>
                </div>
                <div class="d-flex align-items-center mb-3">
                    <div class="bg-info-subtle text-info rounded-circle p-2 me-3">
                        <i class="fas fa-phone fa-fw"></i>
                    </div>
                    <div>
                        <div class="small text-muted">Phone Number</div>
                        <div class="fw-bold text-dark">{{ $booking->contact_phone }}</div>
                    </div>
                </div>
                @if($booking->contact_email)
                <div class="d-flex align-items-center">
                    <div class="bg-warning-subtle text-warning rounded-circle p-2 me-3">
                        <i class="fas fa-envelope fa-fw"></i>
                    </div>
                    <div>
                        <div class="small text-muted">Email Address</div>
                        <div class="fw-bold text-dark text-break">{{ $booking->contact_email }}</div>
                    </div>
                </div>
                @endif
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-body small">
                <div class="text-muted mb-2">
                    <i class="fas fa-info-circle me-1 opacity-50"></i> Booking metadata
                </div>
                <div class="mb-1">Recorded by: <strong>{{ $booking->createdBy->name ?? 'System' }}</strong></div>
                <div class="mb-1">Last update: <strong>{{ $booking->updated_at->format('M d, Y h:i A') }}</strong></div>
            </div>
        </div>
    </div>
</div>
@endsection

