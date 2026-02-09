@extends('layouts.app')
@section('title', __('messages.Dashboard'))
@section('content')
<h1 class="h3 mb-4">{{ __('messages.Dashboard') }}</h1>

{{-- Profile picture & welcome (guest-style) --}}
<div class="text-center mb-4 py-4">
    <a href="{{ route('profile.edit') }}" class="d-inline-block text-decoration-none">
        <img src="{{ auth()->user()->profile_pic_url }}" alt="{{ auth()->user()->name }}" class="rounded-circle shadow-sm border border-3 border-primary" width="120" height="120" style="object-fit: cover;">
    </a>
    <h2 class="h4 mt-3 mb-1">{{ __('Welcome') }}, {{ auth()->user()->name }}</h2>
    <p class="text-muted mb-2">{{ auth()->user()->email }}</p>
    <a href="{{ route('profile.edit') }}" class="btn btn-outline-primary btn-sm">{{ __('My profile') }}</a>
</div>

{{-- Quick actions (guest-style cards) --}}
<div class="mb-4">
    <a href="{{ route('housekeeping.index', ['date' => $today->format('Y-m-d')]) }}" class="card text-decoration-none text-dark border-primary shadow-sm hover-shadow">
        <div class="card-body d-flex align-items-center gap-3 py-4">
            <div class="rounded-circle bg-primary bg-opacity-10 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 56px; height: 56px;">
                <i class="bi bi-bucket fs-4 text-primary"></i>
            </div>
            <div class="flex-grow-1">
                <h2 class="h5 mb-1">{{ __('Room cleaning assignments') }}</h2>
                <p class="text-muted small mb-0">{{ __('View and manage your room cleaning tasks for today.') }}</p>
            </div>
            <i class="bi bi-chevron-right text-primary flex-shrink-0"></i>
        </div>
    </a>
</div>

<div class="mb-4">
    <a href="{{ route('maintenance.index') }}" class="card text-decoration-none text-dark border-primary shadow-sm hover-shadow">
        <div class="card-body d-flex align-items-center gap-3 py-4">
            <div class="rounded-circle bg-primary bg-opacity-10 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 56px; height: 56px;">
                <i class="bi bi-tools fs-4 text-primary"></i>
            </div>
            <div class="flex-grow-1">
                <h2 class="h5 mb-1">{{ __('messages.Maintenance') }}</h2>
                <p class="text-muted small mb-0">{{ __('View maintenance requests assigned to you.') }}</p>
            </div>
            <i class="bi bi-chevron-right text-primary flex-shrink-0"></i>
        </div>
    </a>
</div>

{{-- Today's assignments --}}
<div class="row mb-4">
    <div class="col-12">
        <div class="card h-100 border-0 shadow-sm">
            <div class="card-body">
                <h2 class="h5 mb-3">{{ __('Today’s assignments') }} – {{ $today->format('l, M j, Y') }}</h2>
                @forelse($todayAssignments as $a)
                    @php $cfg = \App\Models\HousekeepingAssignment::statusBadgeConfig($a->status); @endphp
                    <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-2">
                        <div>
                            <span class="fw-semibold">{{ __('messages.Room') }} {{ $a->room->number ?? '-' }}</span>
                            <span class="text-muted small ms-2">{{ $a->room->roomType->name ?? '' }}</span>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge {{ $cfg['class'] }}">{{ __($cfg['label']) }}</span>
                            <a href="{{ route('housekeeping.index', ['date' => $today->format('Y-m-d')]) }}" class="btn btn-sm btn-outline-primary">{{ __('messages.View') }}</a>
                        </div>
                    </div>
                @empty
                    <p class="text-muted mb-0">{{ __('No assignments for today.') }}</p>
                @endforelse
                <a href="{{ route('housekeeping.index', ['date' => $today->format('Y-m-d')]) }}" class="btn btn-sm btn-link px-0 mt-2">{{ __('View all') }}</a>
            </div>
        </div>
    </div>
</div>
@endsection
