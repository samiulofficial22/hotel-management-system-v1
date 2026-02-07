@extends('layouts.app')
@section('title', __('My bookings'))
@section('content')
<h1 class="h3 mb-4">{{ __('My bookings') }}</h1>

@if(!$guest)
    <div class="alert alert-warning">
        {{ __('No guest profile is linked to your account yet. Please contact the hotel.') }}
    </div>
@else
    <div class="table-responsive">
        <table class="table table-striped align-middle">
            <thead>
                <tr>
                    <th>{{ __('Booking #') }}</th>
                    <th>{{ __('Booked on') }}</th>
                    <th>{{ __('Room') }}</th>
                    <th>{{ __('Check-in') }}</th>
                    <th>{{ __('Check-out') }}</th>
                    <th>{{ __('Status') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($bookings as $b)
                    @php($badge = \App\Models\Booking::statusBadgeConfig($b->status))
                    <tr>
                        <td>{{ $b->booking_number }}</td>
                        <td>{{ $b->created_at?->format('d M Y') ?? '-' }}</td>
                        <td>{{ optional($b->room)->number ?? '-' }}</td>
                        <td>{{ $b->check_in_date?->format('Y-m-d') ?? '-' }}</td>
                        <td>{{ $b->check_out_date?->format('Y-m-d') ?? '-' }}</td>
                        <td><span class="badge {{ $badge['class'] }}">{{ __($badge['label']) }}</span></td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-muted">{{ __('No bookings found.') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($bookings instanceof \Illuminate\Contracts\Pagination\Paginator)
        {{ $bookings->links() }}
    @endif
@endif
@endsection

