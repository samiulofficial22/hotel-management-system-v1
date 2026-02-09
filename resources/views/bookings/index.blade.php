@extends('layouts.app')
@section('title', 'Bookings')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0">Bookings</h1>
        <p class="text-muted small mb-0">Total: <strong>{{ $bookings->total() }}</strong> {{ $bookings->total() === 1 ? 'booking' : 'bookings' }}</p>
    </div>
    <div class="d-flex gap-2 flex-wrap">
        @can('guest.manage')
        <a href="{{ route('guest.requests.index') }}" class="btn btn-outline-warning">{{ __('messages.Guest requests') }}</a>
        @endcan
        <a href="{{ route('bookings.create') }}" class="btn btn-primary">New Booking</a>
        <a href="{{ route('bookings.calendar') }}" class="btn btn-outline-primary">Calendar</a>
    </div>
</div>

<form action="{{ route('bookings.index') }}" method="GET" class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <div class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label small text-muted">Search (booking #, guest, room)</label>
                <input type="text" name="q" class="form-control form-control-sm" placeholder="Search..." value="{{ request('q') }}">
            </div>
            <div class="col-md-2">
                <label class="form-label small text-muted">Status</label>
                <select name="status" class="form-select form-select-sm">
                    <option value="">All</option>
                    @foreach([\App\Models\Booking::STATUS_PENDING, \App\Models\Booking::STATUS_CONFIRMED, \App\Models\Booking::STATUS_CHECKED_IN, \App\Models\Booking::STATUS_CHECKED_OUT, \App\Models\Booking::STATUS_CANCELLED, \App\Models\Booking::STATUS_NO_SHOW] as $s)
                    @php $cfg = \App\Models\Booking::statusBadgeConfig($s); @endphp
                    <option value="{{ $s }}" {{ request('status') == $s ? 'selected' : '' }}>{{ $cfg['label'] }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary btn-sm w-100">Search</button>
            </div>
            @if(request()->hasAny(['q', 'status']))
            <div class="col-md-1">
                <a href="{{ route('bookings.index') }}" class="btn btn-outline-secondary btn-sm w-100">Clear</a>
            </div>
            @endif
        </div>
    </div>
</form>

<div class="table-responsive">
<table class="table table-striped">
<thead><tr><th>ID</th><th>Booking #</th><th>Guest</th><th>Room</th><th>Check-in</th><th>Check-out</th><th>Status</th><th></th></tr></thead>
<tbody>
@foreach($bookings as $b)
<tr>
<td>{{ ($bookings->currentPage() - 1) * $bookings->perPage() + $loop->iteration }}</td>
<td>{{ $b->booking_number }}</td>
<td>{{ $b->guest->full_name ?? '-' }}</td>
<td>{{ $b->room->number ?? '-' }}</td>
<td>{{ $b->check_in_date->format('Y-m-d') }}</td>
<td>{{ $b->check_out_date->format('Y-m-d') }}</td>
<td>
    @php $cfg = \App\Models\Booking::statusBadgeConfig($b->status); @endphp
    <span class="badge {{ $cfg['class'] }}">{{ $cfg['label'] }}</span>
</td>
<td><a href="{{ route('bookings.show', $b) }}" class="btn btn-sm btn-outline-primary">View</a> <a href="{{ route('bookings.edit', $b) }}" class="btn btn-sm btn-outline-secondary">Edit</a></td>
</tr>
@endforeach
</tbody>
</table>
</div>
{{ $bookings->links() }}
@endsection
