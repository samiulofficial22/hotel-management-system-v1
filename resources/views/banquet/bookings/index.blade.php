@extends('layouts.app')
@section('title', 'Banquet Bookings')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0">Banquet Bookings</h1>
        <p class="text-muted small mb-0">Total: <strong>{{ $bookings->total() }}</strong> bookings found</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('banquet.venues.index') }}" class="btn btn-outline-primary shadow-sm">
            <i class="fas fa-hotel me-1"></i> Venues
        </a>
        <a href="{{ route('banquet.bookings.create') }}" class="btn btn-primary shadow-sm">
            <i class="fas fa-calendar-plus me-1"></i> New Booking
        </a>
    </div>
</div>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form action="{{ route('banquet.bookings.index') }}" method="GET">
            <div class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label small fw-bold text-muted text-uppercase">Search</label>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-white border-end-0"><i class="fas fa-search text-muted"></i></span>
                        <input type="text" name="q" class="form-control border-start-0 ps-0" placeholder="Event, contact, or venue name..." value="{{ request('q') }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-bold text-muted text-uppercase">Show Status</label>
                    <select name="status" class="form-select form-select-sm">
                        <option value="">All Statuses</option>
                        @foreach([\App\Models\BanquetBooking::STATUS_PENDING, \App\Models\BanquetBooking::STATUS_CONFIRMED, \App\Models\BanquetBooking::STATUS_CANCELLED, \App\Models\BanquetBooking::STATUS_COMPLETED] as $s)
                        @php $cfg = \App\Models\BanquetBooking::statusBadgeConfig($s); @endphp
                        <option value="{{ $s }}" {{ request('status') == $s ? 'selected' : '' }}>{{ $cfg['label'] }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary btn-sm w-100 shadow-sm">
                        <i class="fas fa-filter me-1"></i> Filter
                    </button>
                </div>
                @if(request()->hasAny(['q', 'status']))
                <div class="col-md-1">
                    <a href="{{ route('banquet.bookings.index') }}" class="btn btn-light btn-sm w-100 text-muted">
                        Clear
                    </a>
                </div>
                @endif
            </div>
        </form>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4">Event & Venue</th>
                        <th>Contact</th>
                        <th>Date & Time</th>
                        <th>Guests</th>
                        <th>Status</th>
                        <th class="text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($bookings as $b)
                    <tr>
                        <td class="ps-4">
                            <div class="fw-bold text-dark">{{ $b->event_name }}</div>
                            <div class="small text-primary"><i class="fas fa-map-marker-alt me-1 opacity-50"></i>{{ $b->banquetVenue->name ?? 'N/A' }}</div>
                        </td>
                        <td>
                            <div class="small fw-bold">{{ $b->contact_name }}</div>
                            <div class="small text-muted"><i class="fas fa-phone-alt me-1 opacity-50"></i>{{ $b->contact_phone }}</div>
                        </td>
                        <td>
                            <div class="small fw-bold"><i class="far fa-calendar-alt me-1 opacity-50"></i>{{ $b->event_date->format('M d, Y') }}</div>
                            <div class="small text-muted">
                                <i class="far fa-clock me-1 opacity-50"></i>
                                {{ \Carbon\Carbon::parse($b->start_time)->format('h:i A') }} - {{ \Carbon\Carbon::parse($b->end_time)->format('h:i A') }}
                            </div>
                        </td>
                        <td>
                            <span class="badge bg-secondary-subtle text-secondary rounded-pill px-3">
                                <i class="fas fa-user-friends me-1"></i> {{ $b->guest_count }}
                            </span>
                        </td>
                        <td>
                            @php $cfg = \App\Models\BanquetBooking::statusBadgeConfig($b->status); @endphp
                            <span class="badge {{ $cfg['class'] }} rounded-pill px-3 shadow-sm" style="font-size: 0.75rem;">
                                {{ $cfg['label'] }}
                            </span>
                        </td>
                        <td class="text-end pe-4">
                            <div class="btn-group">
                                <a href="{{ route('banquet.bookings.show', $b) }}" class="btn btn-sm btn-light" title="View Details">
                                    <i class="fas fa-eye text-primary"></i>
                                </a>
                                <a href="{{ route('banquet.bookings.edit', $b) }}" class="btn btn-sm btn-light" title="Edit Booking">
                                    <i class="fas fa-edit text-secondary"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                    @if($bookings->isEmpty())
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">
                            <i class="far fa-calendar-times fa-3x mb-3 d-block opacity-25"></i>
                            No banquet bookings found for the selected criteria.
                        </td>
                    </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
    @if($bookings->hasPages())
    <div class="card-footer bg-white border-0 py-3">
        {{ $bookings->links() }}
    </div>
    @endif
</div>
@endsection

