@extends('layouts.app')
@section('title', 'Banquet Bookings')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0">Banquet Bookings</h1>
        <p class="text-muted small mb-0">Total: <strong>{{ $bookings->total() }}</strong> {{ $bookings->total() === 1 ? 'booking' : 'bookings' }}</p>
    </div>
    <a href="{{ route('banquet.bookings.create') }}" class="btn btn-primary">New Booking</a>
</div>

<form action="{{ route('banquet.bookings.index') }}" method="GET" class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <div class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label small text-muted">Search (event, contact, venue)</label>
                <input type="text" name="q" class="form-control form-control-sm" placeholder="Search..." value="{{ request('q') }}">
            </div>
            <div class="col-md-2">
                <label class="form-label small text-muted">Status</label>
                <select name="status" class="form-select form-select-sm">
                    <option value="">All</option>
                    @foreach([\App\Models\BanquetBooking::STATUS_PENDING, \App\Models\BanquetBooking::STATUS_CONFIRMED, \App\Models\BanquetBooking::STATUS_CANCELLED, \App\Models\BanquetBooking::STATUS_COMPLETED] as $s)
                    @php $cfg = \App\Models\BanquetBooking::statusBadgeConfig($s); @endphp
                    <option value="{{ $s }}" {{ request('status') == $s ? 'selected' : '' }}>{{ $cfg['label'] }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary btn-sm w-100">Search</button>
            </div>
            @if(request()->hasAny(['q', 'status']))
            <div class="col-md-1">
                <a href="{{ route('banquet.bookings.index') }}" class="btn btn-outline-secondary btn-sm w-100">Clear</a>
            </div>
            @endif
        </div>
    </div>
</form>

<div class="table-responsive">
<table class="table table-striped">
<thead><tr><th>ID</th><th>Event</th><th>Venue</th><th>Date</th><th>Time</th><th>Guests</th><th>Status</th><th></th></tr></thead>
<tbody>
@foreach($bookings as $b)
<tr>
    <td>{{ ($bookings->currentPage() - 1) * $bookings->perPage() + $loop->iteration }}</td>
    <td>{{ $b->event_name }}</td>
    <td>{{ $b->banquetVenue->name ?? '-' }}</td>
    <td>{{ $b->event_date->format('Y-m-d') }}</td>
    <td>{{ \Carbon\Carbon::parse($b->start_time)->format('h:i A') }} - {{ \Carbon\Carbon::parse($b->end_time)->format('h:i A') }}</td>
    <td>{{ $b->guest_count }}</td>
    <td>
        @php $cfg = \App\Models\BanquetBooking::statusBadgeConfig($b->status); @endphp
        <span class="badge {{ $cfg['class'] }}">{{ $cfg['label'] }}</span>
    </td>
    <td><a href="{{ route('banquet.bookings.show', $b) }}" class="btn btn-sm btn-outline-primary">View</a> <a href="{{ route('banquet.bookings.edit', $b) }}" class="btn btn-sm btn-outline-secondary">Edit</a></td>
</tr>
@endforeach
</tbody>
</table>
</div>
{{ $bookings->links() }}
@endsection
