@extends('layouts.app')
@section('title', __('Guest booking requests'))
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">{{ __('Guest booking requests') }}</h1>
</div>

<div class="table-responsive">
    <table class="table table-striped align-middle">
        <thead>
            <tr>
                <th>{{ __('Booking #') }}</th>
                <th>{{ __('Guest') }}</th>
                <th>{{ __('Room') }}</th>
                <th>{{ __('Check-in') }}</th>
                <th>{{ __('Check-out') }}</th>
                <th>{{ __('Status') }}</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @forelse($bookings as $b)
                @php($badge = \App\Models\Booking::statusBadgeConfig($b->status))
                <tr>
                    <td>{{ $b->booking_number }}</td>
                    <td>
                        {{ $b->guest?->full_name ?? '-' }}<br>
                        <small class="text-muted">
                            {{ $b->guest?->email ?? $b->guest?->phone ?? '' }}
                        </small>
                    </td>
                    <td>{{ optional($b->room)->number ?? '-' }}</td>
                    <td>{{ $b->check_in_date?->format('Y-m-d') ?? '-' }}</td>
                    <td>{{ $b->check_out_date?->format('Y-m-d') ?? '-' }}</td>
                    <td><span class="badge {{ $badge['class'] }}">{{ __($badge['label']) }}</span></td>
                    <td class="text-end">
                        @can('guest.manage')
                        <form action="{{ route('guest.requests.approve', $b) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-success">{{ __('Approve') }}</button>
                        </form>
                        <form action="{{ route('guest.requests.reject', $b) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('Are you sure you want to reject this request?') }}');">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-outline-danger">{{ __('Reject') }}</button>
                        </form>
                        @endcan
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-muted">{{ __('No pending booking requests.') }}</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{ $bookings->links() }}
@endsection

