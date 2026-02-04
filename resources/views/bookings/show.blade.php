@extends('layouts.app')
@section('title', 'Booking')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Booking {{ $booking->booking_number }}</h1>
    <div>
        @if($booking->status === 'confirmed')
        <form action="{{ route('bookings.check-in', $booking) }}" method="POST" class="d-inline">@csrf<button type="submit" class="btn btn-success">Check-in</button></form>
        @endif
        @if($booking->status === 'checked_in')
        <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#checkoutModal">Check-out</button>
        @endif
        <a href="{{ route('bookings.edit', $booking) }}" class="btn btn-outline-secondary">Edit</a>
    </div>
</div>
<div class="card mb-4"><div class="card-body">
    <p><strong>Guest:</strong> {{ $booking->guest->full_name ?? '-' }}</p>
    <p><strong>Room:</strong> {{ $booking->room->number ?? '-' }} ({{ $booking->room->roomType->name ?? '' }})</p>
    <p><strong>Check-in:</strong> {{ $booking->check_in_date->format('Y-m-d') }} <strong>Check-out:</strong> {{ $booking->check_out_date->format('Y-m-d') }}</p>
    <p><strong>Status:</strong> {{ $booking->status }} <strong>Room Rate:</strong> {{ number_format($booking->room_rate, 2) }}</p>
</div></div>
<h5>Invoices</h5>
@forelse($booking->invoices as $inv)
<div class="card mb-2"><div class="card-body d-flex justify-content-between align-items-center">
    <span>{{ $inv->invoice_number }} – {{ number_format($inv->total_amount, 2) }} ({{ $inv->status }})</span>
    @can('invoices.manage')
    <a href="{{ route('invoices.pdf', $inv) }}" class="btn btn-sm btn-outline-primary" target="_blank">Download PDF</a>
    @endcan
</div></div>
@empty
<p class="text-muted">No invoices yet.</p>
@endforelse

<div class="modal fade" id="checkoutModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('bookings.check-out', $booking) }}" method="POST">
                @csrf
                <div class="modal-header"><h5 class="modal-title">Check-out</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body"><label class="form-label">Late checkout fee (optional)</label><input type="number" step="0.01" name="late_checkout_fee" class="form-control" value="0"></div>
                <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button><button type="submit" class="btn btn-warning">Check-out</button></div>
            </form>
        </div>
    </div>
</div>
@endsection
