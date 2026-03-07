@extends('layouts.app')
@section('title', 'Banquet Venues')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0">Banquet Venues</h1>
        <p class="text-muted small mb-0">Manage your event spaces and halls</p>
    </div>
    <div>
        <a href="{{ route('banquet.bookings.index') }}" class="btn btn-outline-primary shadow-sm me-2">
            <i class="fas fa-calendar-alt me-1"></i> Bookings
        </a>
        <a href="{{ route('banquet.venues.create') }}" class="btn btn-primary shadow-sm">
            <i class="fas fa-plus me-1"></i> Add Venue
        </a>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4">Venue Name</th>
                        <th>Location</th>
                        <th>Capacity</th>
                        <th>Rates</th>
                        <th>Status</th>
                        <th class="text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($venues as $v)
                    <tr>
                        <td class="ps-4">
                            <div class="fw-bold text-primary">{{ $v->name }}</div>
                            @if($v->description)
                            <div class="small text-muted text-truncate" style="max-width: 200px;">{{ $v->description }}</div>
                            @endif
                        </td>
                        <td>{{ $v->location ?? 'N/A' }}</td>
                        <td>
                            <span class="badge bg-info-subtle text-info rounded-pill px-3">
                                <i class="fas fa-users me-1"></i> {{ $v->capacity }}
                            </span>
                        </td>
                        <td>
                            @if($v->hourly_rate)
                            <div class="small">Hourly: <strong>${{ number_format($v->hourly_rate, 2) }}</strong></div>
                            @endif
                            @if($v->fixed_rate)
                            <div class="small">Fixed: <strong>${{ number_format($v->fixed_rate, 2) }}</strong></div>
                            @endif
                            @if(!$v->hourly_rate && !$v->fixed_rate)
                            <span class="text-muted small">Not specified</span>
                            @endif
                        </td>
                        <td>
                            @if($v->is_active)
                            <span class="badge bg-success">Active</span>
                            @else
                            <span class="badge bg-danger">Inactive</span>
                            @endif
                        </td>
                        <td class="text-end pe-4">
                            <div class="btn-group">
                                <a href="{{ route('banquet.venues.edit', $v) }}" class="btn btn-sm btn-light">
                                    <i class="fas fa-edit text-primary"></i>
                                </a>
                                <form action="{{ route('banquet.venues.destroy', $v) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this venue?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-light">
                                        <i class="fas fa-trash text-danger"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                    @if($venues->isEmpty())
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">
                            <i class="fas fa-hotel fa-3x mb-3 d-block opacity-25"></i>
                            No banquet venues found. Start by adding one!
                        </td>
                    </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

