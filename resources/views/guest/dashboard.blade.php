@extends('layouts.app')
@section('title', __('Guest Dashboard'))
@section('content')
<h1 class="h3 mb-4">{{ __('Guest Dashboard') }}</h1>

@if(!$guest)
    <div class="alert alert-warning">
        {{ __('No guest profile is linked to your account yet. Please contact the hotel.') }}
    </div>
@else
    <div class="row mb-4">
        <div class="col-md-6 mb-3">
            <div class="card h-100">
                <div class="card-body">
                    <h2 class="h5 mb-2">{{ __('Welcome') }}, {{ $guest->full_name }}</h2>
                    <p class="mb-1 text-muted">{{ $guest->email ?? $guest->phone ?? '' }}</p>
                    <a href="{{ route('guest.profile') }}" class="btn btn-sm btn-outline-primary mt-2">{{ __('My profile') }}</a>
                </div>
            </div>
        </div>
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
                    <a href="{{ route('guest.bookings') }}" class="btn btn-sm btn-link px-0 mt-2">{{ __('View all bookings') }}</a>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
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
@endif
@endsection

