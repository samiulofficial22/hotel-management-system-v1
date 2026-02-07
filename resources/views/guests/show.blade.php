@extends('layouts.app')
@section('title', $guest->full_name)
@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
    <h1 class="h3 mb-0">{{ $guest->full_name }}</h1>
    <div class="d-flex flex-wrap gap-2">
        <a href="{{ route('guests.edit', $guest) }}" class="btn btn-primary btn-sm">{{ __('messages.Edit') }}</a>
        @if($guest->user_id)
            <form action="{{ route('guests.revoke-portal', $guest) }}" method="POST" class="d-inline"
                  onsubmit="return confirm('{{ __('messages.Remove portal access for this guest?') }}');">
                @csrf
                <button type="submit" class="btn btn-outline-warning btn-sm">{{ __('messages.Remove Portal Access') }}</button>
            </form>
        @endif
        <form action="{{ route('guests.destroy', $guest) }}" method="POST" class="d-inline"
              onsubmit="return confirm('{{ __('messages.Delete this guest? This cannot be undone.') }}');">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger btn-sm">{{ __('messages.Delete') }}</button>
        </form>
    </div>
</div>

<div class="row g-3 mb-4">
    {{-- Personal information --}}
    <div class="col-md-6 col-lg-4">
        <div class="card h-100 shadow-sm">
            <div class="card-header bg-light">{{ __('messages.Personal information') }}</div>
            <div class="card-body">
                <dl class="mb-0 small">
                    <dt class="text-muted">{{ __('messages.First name') }}</dt>
                    <dd class="mb-2">{{ $guest->first_name ?? '-' }}</dd>
                    <dt class="text-muted">{{ __('messages.Last name') }}</dt>
                    <dd class="mb-2">{{ $guest->last_name ?? '-' }}</dd>
                    @if($guest->date_of_birth)
                    <dt class="text-muted">{{ __('messages.Date of birth') }}</dt>
                    <dd class="mb-2">{{ $guest->date_of_birth->format('Y-m-d') }}</dd>
                    @endif
                    @if($guest->nationality)
                    <dt class="text-muted">{{ __('messages.Nationality') }}</dt>
                    <dd class="mb-0">{{ $guest->nationality }}</dd>
                    @endif
                </dl>
            </div>
        </div>
    </div>

    {{-- Contact --}}
    <div class="col-md-6 col-lg-4">
        <div class="card h-100 shadow-sm">
            <div class="card-header bg-light">{{ __('messages.Contact') }}</div>
            <div class="card-body">
                <dl class="mb-0 small">
                    <dt class="text-muted">{{ __('messages.Email') }}</dt>
                    <dd class="mb-2">{{ $guest->email ?? '-' }}</dd>
                    <dt class="text-muted">{{ __('messages.Phone') }}</dt>
                    <dd class="mb-0">{{ $guest->phone ?? '-' }}</dd>
                </dl>
            </div>
        </div>
    </div>

    {{-- Address --}}
    <div class="col-md-6 col-lg-4">
        <div class="card h-100 shadow-sm">
            <div class="card-header bg-light">{{ __('messages.Address') }}</div>
            <div class="card-body">
                <dl class="mb-0 small">
                    @if($guest->address)
                    <dt class="text-muted">{{ __('messages.Address') }}</dt>
                    <dd class="mb-2">{{ $guest->address }}</dd>
                    @endif
                    @if($guest->city)
                    <dt class="text-muted">{{ __('messages.City') }}</dt>
                    <dd class="mb-2">{{ $guest->city }}</dd>
                    @endif
                    @if($guest->country)
                    <dt class="text-muted">{{ __('messages.Country') }}</dt>
                    <dd class="mb-0">{{ $guest->country }}</dd>
                    @endif
                    @if(!$guest->address && !$guest->city && !$guest->country)
                    <dd class="text-muted mb-0">-</dd>
                    @endif
                </dl>
            </div>
        </div>
    </div>

    {{-- ID / NID --}}
    <div class="col-md-6 col-lg-4">
        <div class="card h-100 shadow-sm">
            <div class="card-header bg-light">{{ __('messages.ID / NID') }}</div>
            <div class="card-body">
                <dl class="mb-0 small">
                    @if($guest->id_type)
                    <dt class="text-muted">{{ __('messages.ID type') }}</dt>
                    <dd class="mb-2">{{ $guest->id_type }}</dd>
                    @endif
                    @if($guest->id_number)
                    <dt class="text-muted">{{ __('messages.ID number') }}</dt>
                    <dd class="mb-2">{{ $guest->id_number }}</dd>
                    @endif
                    @if($guest->nid_number)
                    <dt class="text-muted">{{ __('messages.NID number') }}</dt>
                    <dd class="mb-0">{{ $guest->nid_number }}</dd>
                    @endif
                    @if(!$guest->id_type && !$guest->id_number && !$guest->nid_number)
                    <dd class="text-muted mb-0">-</dd>
                    @endif
                </dl>
            </div>
        </div>
    </div>

    {{-- Portal access --}}
    <div class="col-md-6 col-lg-4">
        <div class="card h-100 shadow-sm">
            <div class="card-header bg-light">{{ __('messages.Portal access') }}</div>
            <div class="card-body">
                @if($guest->user_id)
                    <p class="mb-1 small"><span class="badge bg-success">{{ __('messages.Active') }}</span></p>
                    <p class="mb-0 small text-muted">{{ __('messages.Guest can log in to the portal.') }}</p>
                @else
                    <p class="mb-0 small text-muted">-</p>
                @endif
            </div>
        </div>
    </div>

    {{-- Notes --}}
    @if($guest->notes)
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-header bg-light">{{ __('messages.Notes') }}</div>
            <div class="card-body">
                <p class="mb-0 small">{{ $guest->notes }}</p>
            </div>
        </div>
    </div>
    @endif
</div>

{{-- NID Photos --}}
@if($guest->nid_photo_front || $guest->nid_photo_back)
<div class="mb-4">
    <h5 class="mb-3">{{ __('messages.NID photos') }}</h5>
    <div class="row g-3">
        @if($guest->nid_photo_front)
        <div class="col-md-6">
            <div class="card border shadow-sm">
                <div class="card-header small bg-light py-1">{{ __('messages.NID – Front') }}</div>
                <div class="card-body p-2 text-center">
                    <a href="{{ asset('storage/' . $guest->nid_photo_front) }}" target="_blank" class="d-inline-block">
                        <img src="{{ asset('storage/' . $guest->nid_photo_front) }}" alt="NID Front" class="img-fluid rounded" style="max-height: 280px; object-fit: contain;">
                    </a>
                    <small class="d-block mt-1"><a href="{{ asset('storage/' . $guest->nid_photo_front) }}" target="_blank">{{ __('messages.Open full size') }}</a></small>
                </div>
            </div>
        </div>
        @endif
        @if($guest->nid_photo_back)
        <div class="col-md-6">
            <div class="card border shadow-sm">
                <div class="card-header small bg-light py-1">{{ __('messages.NID – Back') }}</div>
                <div class="card-body p-2 text-center">
                    <a href="{{ asset('storage/' . $guest->nid_photo_back) }}" target="_blank" class="d-inline-block">
                        <img src="{{ asset('storage/' . $guest->nid_photo_back) }}" alt="NID Back" class="img-fluid rounded" style="max-height: 280px; object-fit: contain;">
                    </a>
                    <small class="d-block mt-1"><a href="{{ asset('storage/' . $guest->nid_photo_back) }}" target="_blank">{{ __('messages.Open full size') }}</a></small>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@endif

{{-- Bookings --}}
<h5 class="mb-3">{{ __('messages.Bookings') }}</h5>
@if($guest->bookings->isEmpty())
    <p class="text-muted">{{ __('messages.No bookings yet.') }}</p>
@else
    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>{{ __('messages.Booking #') }}</th>
                    <th>{{ __('messages.Room') }}</th>
                    <th>{{ __('messages.Check-in') }}</th>
                    <th>{{ __('messages.Check-out') }}</th>
                    <th>{{ __('messages.Status') }}</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach($guest->bookings as $b)
                    @php($cfg = \App\Models\Booking::statusBadgeConfig($b->status))
                    <tr>
                        <td>{{ $b->booking_number }}</td>
                        <td>{{ $b->room->number ?? '-' }} @if($b->room?->roomType)<small class="text-muted">({{ $b->room->roomType->name }})</small>@endif</td>
                        <td>{{ $b->check_in_date?->format('Y-m-d') ?? '-' }}</td>
                        <td>{{ $b->check_out_date?->format('Y-m-d') ?? '-' }}</td>
                        <td><span class="badge {{ $cfg['class'] }}">{{ __($cfg['label']) }}</span></td>
                        <td><a href="{{ route('bookings.show', $b) }}" class="btn btn-sm btn-outline-primary">{{ __('messages.View') }}</a></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endif
@endsection
