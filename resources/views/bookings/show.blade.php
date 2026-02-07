@extends('layouts.app')

@section('title', 'Booking '.$booking->booking_number)

@section('content')
@php
    $badge = \App\Models\Booking::statusBadgeConfig($booking->status);
@endphp

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Booking {{ $booking->booking_number }}</h1>
    <div class="d-flex gap-2 align-items-center">
        <span class="badge {{ $badge['class'] }}">{{ $badge['label'] }}</span>

        @if($booking->status === \App\Models\Booking::STATUS_CONFIRMED)
            <form action="{{ route('bookings.check-in', $booking) }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-success btn-sm">Check-in</button>
            </form>
        @endif

        @if($booking->status === \App\Models\Booking::STATUS_CHECKED_IN)
            <button type="button" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#checkoutModal">
                Check-out
            </button>
        @endif
        <a href="{{ route('bookings.edit', $booking) }}" class="btn btn-outline-secondary btn-sm">Edit</a>
        <form action="{{ route('bookings.destroy', $booking) }}" method="POST" class="d-inline"
              onsubmit="return confirm('Delete this booking? This cannot be undone.');">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger btn-sm">Delete</button>
        </form>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-6 col-lg-4">
        <div class="card h-100">
            <div class="card-header bg-light">Stay details</div>
            <div class="card-body small">
                <p class="mb-1"><strong>Check-in:</strong> {{ $booking->check_in_date?->format('Y-m-d') ?? '-' }}</p>
                <p class="mb-1"><strong>Check-out:</strong> {{ $booking->check_out_date?->format('Y-m-d') ?? '-' }}</p>
                @if($booking->check_in_date && $booking->check_out_date)
                    <p class="mb-0"><strong>Nights:</strong> {{ $booking->check_in_date->diffInDays($booking->check_out_date) }}</p>
                @endif
            </div>
        </div>
    </div>

    <div class="col-md-6 col-lg-4">
        <div class="card h-100">
            <div class="card-header bg-light">Guest & occupancy</div>
            <div class="card-body small">
                <p class="mb-1"><strong>Guest:</strong> {{ $booking->guest->full_name ?? '-' }}</p>
                <p class="mb-1"><strong>Email:</strong> {{ $booking->guest->email ?? '-' }}</p>
                <p class="mb-1"><strong>Phone:</strong> {{ $booking->guest->phone ?? '-' }}</p>
                <p class="mb-1"><strong>Adults:</strong> {{ $booking->adults ?? 1 }}</p>
                <p class="mb-0"><strong>Children:</strong> {{ $booking->children ?? 0 }}</p>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-lg-4">
        <div class="card h-100">
            <div class="card-header bg-light">Room</div>
            <div class="card-body small">
                <p class="mb-1">
                    <strong>Room:</strong>
                    {{ $booking->room->number ?? '-' }}
                    @if($booking->room?->roomType)
                        <small class="text-muted">({{ $booking->room->roomType->name }})</small>
                    @endif
                </p>
                <p class="mb-1"><strong>Room rate:</strong> {{ number_format($booking->room_rate, 2) }}</p>
                <p class="mb-0"><strong>Status:</strong> {{ $badge['label'] }}</p>
            </div>
        </div>
    </div>

    @if($booking->special_requests || $booking->internal_notes)
    <div class="col-12">
        <div class="card">
            <div class="card-header bg-light">Notes</div>
            <div class="card-body small">
                @if($booking->special_requests)
                    <p class="mb-2"><strong>Special requests:</strong> {{ $booking->special_requests }}</p>
                @endif
                @if($booking->internal_notes)
                    <p class="mb-0 text-muted"><strong>Internal notes:</strong> {{ $booking->internal_notes }}</p>
                @endif
            </div>
        </div>
    </div>
    @endif
</div>

<h5 class="mb-3">Invoices & payments</h5>
@if($booking->invoices && $booking->invoices->isNotEmpty())
    <div class="table-responsive mb-3">
        <table class="table table-striped align-middle">
            <thead>
                <tr>
                    <th>Invoice #</th>
                    <th>Total</th>
                    <th>Paid</th>
                    <th>Balance</th>
                    <th>Status</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach($booking->invoices as $inv)
                    @php
                        $paid = $inv->payments->sum('amount');
                        $balance = $inv->total_amount - $paid;
                    @endphp
                    <tr>
                        <td>{{ $inv->invoice_number }}</td>
                        <td>{{ number_format($inv->total_amount, 2) }}</td>
                        <td>{{ number_format($paid, 2) }}</td>
                        <td>{{ number_format($balance, 2) }}</td>
                        <td>{{ $inv->status }}</td>
                        <td class="text-end">
                            @can('invoices.manage')
                            <a href="{{ route('invoices.pdf', $inv) }}" class="btn btn-sm btn-outline-primary" target="_blank">
                                Download PDF
                            </a>
                            @endcan
                        </td>
                    </tr>
                @endforeach 
            </tbody>
        </table>
    </div>
@else
    <p class="text-muted">No invoices yet.</p>
@endif

<div class="modal fade" id="checkoutModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('bookings.check-out', $booking) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Check-out</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <label class="form-label">Late checkout fee (optional)</label>
                    <input type="number" step="0.01" name="late_checkout_fee" class="form-control" value="0">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning">Check-out</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

