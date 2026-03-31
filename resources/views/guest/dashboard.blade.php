@extends('layouts.app')
@section('title', __('Guest Dashboard'))
@section('content')
<h1 class="h3 mb-4">{{ __('Guest Dashboard') }}</h1>

@if(!$guest)
    <div class="alert alert-warning">
        {{ __('No guest profile is linked to your account yet. Please contact the hotel.') }}
    </div>
@else
{{-- Profile picture & welcome in the middle --}}
<div class="text-center mb-4 py-4">
    <a href="{{ route('guest.profile') }}" class="d-inline-block text-decoration-none">
        <img src="{{ auth()->user()->profile_pic_url }}" alt="{{ $guest->full_name }}"
            class="rounded-circle shadow-sm border border-3 border-primary" width="120" height="120"
            style="object-fit: cover;">
    </a>
    <h2 class="h4 mt-3 mb-1">{{ __('Welcome') }}, {{ $guest->full_name }}</h2>
    <p class="text-muted mb-2">
        {{ $guest->email ?? $guest->phone ?? '' }} <br>
        <span
            class="badge bg-secondary mt-1">{{ auth()->user()->roles->pluck('name')->join(', ') ?: __('Guest') }}</span>
    </p>
    <a href="{{ route('guest.profile') }}" class="btn btn-outline-primary btn-sm">{{ __('My profile') }}</a>
</div>

{{-- Book a room --}}
<div class="mb-4">
    <a href="{{ route('booking.request') }}"
        class="card text-decoration-none text-dark border-primary shadow-sm hover-shadow">
        <div class="card-body d-flex align-items-center gap-3 py-4">
            <div class="rounded-circle bg-primary bg-opacity-10 d-flex align-items-center justify-content-center flex-shrink-0"
                style="width: 56px; height: 56px;">
                <i class="bi bi-calendar-plus fs-4 text-primary"></i>
            </div>
            <div class="flex-grow-1">
                <h2 class="h5 mb-1">{{ __('Book a room') }}</h2>
                <p class="text-muted small mb-0">{{ __('Request a new room booking. We will confirm after review.') }}
                </p>
            </div>
            <i class="bi bi-chevron-right text-primary flex-shrink-0"></i>
        </div>
    </a>
</div>

<div class="row mb-4">
    <div class="col-md-6 mb-3">
        <div class="card h-100">
            <div class="card-body">
                <h2 class="h5 mb-3">{{ __('Upcoming bookings') }}</h2>
                @forelse($upcomingBookings as $b)
                @php($badge = \App\Models\Booking::statusBadgeConfig($b->status))
                <div class="d-flex justify-content-between small mb-2">
                    <div>
                        <div class="fw-semibold">{{ $b->booking_number }}</div>
                        <div class="text-muted">
                            {{ optional($b->room)->number ? __('Room') . ' ' . $b->room->number : '' }}
                        </div>
                        <div class="text-muted">
                            {{ $b->check_in_date?->format('Y-m-d') }} → {{ $b->check_out_date?->format('Y-m-d') }}
                        </div>
                    </div>
                    <span class="badge {{ $badge['class'] }}">{{ __($badge['label']) }}</span>
                </div>
                @empty
                <p class="text-muted mb-0">{{ __('No upcoming bookings.') }}</p>
                @endforelse
                <a href="{{ route('guest.bookings') }}"
                    class="btn btn-sm btn-link px-0 mt-2">{{ __('View all bookings') }}</a>
            </div>
        </div>
    </div>
    <div class="col-md-6 mb-3">
        <div class="card h-100">
            <div class="card-body">
                <h2 class="h5 mb-3">{{ __('Recent stays') }}</h2>
                @forelse($pastBookings as $b)
                @php($badge = \App\Models\Booking::statusBadgeConfig($b->status))
                <div class="d-flex justify-content-between small mb-2">
                    <div>
                        <div class="fw-semibold">{{ $b->booking_number }}</div>
                        <div class="text-muted">
                            {{ optional($b->room)->number ? __('Room') . ' ' . $b->room->number : '' }}
                        </div>
                        <div class="text-muted">
                            {{ $b->check_in_date?->format('Y-m-d') }} → {{ $b->check_out_date?->format('Y-m-d') }}
                        </div>
                    </div>
                    <span class="badge {{ $badge['class'] }}">{{ __($badge['label']) }}</span>
                </div>
                @empty
                <p class="text-muted mb-0">{{ __('No past bookings yet.') }}</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endif
@endsection