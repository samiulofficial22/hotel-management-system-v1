@extends('layouts.app')
@section('title', __('messages.Dashboard'))

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">{{ __('messages.Dashboard') }}</h1>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <h6 class="text-muted text-uppercase small">{{ __('messages.Occupancy Today') }}</h6>
                <h2 class="mb-0">{{ $occupancy['percentage'] ?? 0 }}%</h2>
                <small>{{ $occupancy['occupied'] ?? 0 }} / {{ $occupancy['total'] ?? 0 }} {{ __('messages.rooms') }}</small>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <h6 class="text-muted text-uppercase small">{{ __('messages.Revenue Today') }}</h6>
                <h2 class="mb-0">{{ number_format($revenueToday, 2) }}</h2>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <h6 class="text-muted text-uppercase small">{{ __('messages.Revenue This Month') }}</h6>
                <h2 class="mb-0">{{ number_format($revenueMonth, 2) }}</h2>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-6 mb-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">{{ __('messages.Check-outs Today') }}</div>
            <div class="card-body p-0">
                @forelse($alerts['check_outs_today'] ?? [] as $b)
                    <div class="d-flex justify-content-between align-items-center border-bottom p-3">
                        <span>{{ $b->guest->full_name ?? 'N/A' }} – {{ __('messages.Room') }} {{ $b->room->number ?? '' }}</span>
                        <a href="{{ route('bookings.show', $b) }}" class="btn btn-sm btn-outline-primary">{{ __('messages.View') }}</a>
                    </div>
                @empty
                    <p class="p-3 mb-0 text-muted">{{ __('messages.No check-outs today.') }}</p>
                @endforelse
            </div>
        </div>
    </div>
    <div class="col-lg-6 mb-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">{{ __('messages.Check-ins Today') }}</div>
            <div class="card-body p-0">
                @forelse($alerts['check_ins_today'] ?? [] as $b)
                    <div class="d-flex justify-content-between align-items-center border-bottom p-3">
                        <span>{{ $b->guest->full_name ?? 'N/A' }} – {{ __('messages.Room') }} {{ $b->room->number ?? '' }}</span>
                        <a href="{{ route('bookings.show', $b) }}" class="btn btn-sm btn-outline-primary">{{ __('messages.View') }}</a>
                    </div>
                @empty
                    <p class="p-3 mb-0 text-muted">{{ __('messages.No check-ins today.') }}</p>
                @endforelse
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <span class="badge bg-secondary">{{ __('messages.Rooms in cleaning') }}: {{ $alerts['rooms_cleaning'] ?? 0 }}</span>
    </div>
</div>
@endsection
