@extends('layouts.app')
@section('title', __('All Housekeeping Work'))

@push('styles')
<style>
    .stat-card {
        border: none;
        border-radius: 1rem;
        padding: 1.25rem 1.5rem;
        display: flex;
        align-items: center;
        gap: 1rem;
        box-shadow: 0 2px 12px rgba(0,0,0,0.07);
        transition: transform 0.18s ease, box-shadow 0.18s ease;
    }
    .stat-card:hover { transform: translateY(-3px); box-shadow: 0 6px 20px rgba(0,0,0,0.11); }
    .stat-icon {
        width: 52px; height: 52px; border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.4rem; flex-shrink: 0;
    }
    .stat-value { font-size: 1.75rem; font-weight: 700; line-height: 1; }
    .stat-label { font-size: 0.78rem; text-transform: uppercase; letter-spacing: .06em; opacity: .7; margin-top: 2px; }
    .filter-card { border: none; border-radius: 1rem; box-shadow: 0 2px 10px rgba(0,0,0,0.06); }
    .badge-status { font-size: .75rem; padding: .35em .7em; border-radius: .5rem; font-weight: 600; }
    .table thead th { font-size: .78rem; text-transform: uppercase; letter-spacing: .05em; white-space: nowrap; }
    .table tbody tr { transition: background .15s; }
    .table tbody tr:hover { background: rgba(13,110,253,.04); }
    .avatar-sm { width: 30px; height: 30px; border-radius: 50%; object-fit: cover; }
    .avatar-initials {
        width: 30px; height: 30px; border-radius: 50%;
        background: linear-gradient(135deg, #0d6efd, #6610f2);
        color: #fff; font-size: .7rem; font-weight: 700;
        display: flex; align-items: center; justify-content: center; flex-shrink: 0;
    }
</style>
@endpush

@section('content')

{{-- Page header --}}
<div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4">
    <div>
        <h1 class="h3 mb-1 fw-bold"><i class="bi bi-bucket me-2 text-primary"></i>{{ __('All Housekeeping Work') }}</h1>
        <p class="text-muted mb-0 small">{{ __('Full history of all housekeeping assignments across all dates') }}</p>
    </div>
    <a href="{{ route('housekeeping.index') }}" class="btn btn-outline-primary btn-sm">
        <i class="bi bi-calendar-day me-1"></i>{{ __('Today\'s Assignments') }}
    </a>
</div>

{{-- Stats Cards --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="stat-card bg-white">
            <div class="stat-icon" style="background:rgba(13,110,253,.12); color:#0d6efd;">
                <i class="bi bi-list-task"></i>
            </div>
            <div>
                <div class="stat-value text-dark">{{ number_format($stats['total']) }}</div>
                <div class="stat-label text-muted">{{ __('Total Tasks') }}</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card bg-white">
            <div class="stat-icon" style="background:rgba(255,193,7,.15); color:#c79c00;">
                <i class="bi bi-hourglass-split"></i>
            </div>
            <div>
                <div class="stat-value" style="color:#c79c00;">{{ number_format($stats['pending']) }}</div>
                <div class="stat-label text-muted">{{ __('Pending') }}</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card bg-white">
            <div class="stat-icon" style="background:rgba(13,202,240,.12); color:#0a7fa3;">
                <i class="bi bi-arrow-repeat"></i>
            </div>
            <div>
                <div class="stat-value" style="color:#0a7fa3;">{{ number_format($stats['in_progress']) }}</div>
                <div class="stat-label text-muted">{{ __('In Progress') }}</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card bg-white">
            <div class="stat-icon" style="background:rgba(25,135,84,.12); color:#198754;">
                <i class="bi bi-check2-circle"></i>
            </div>
            <div>
                <div class="stat-value text-success">{{ number_format($stats['completed']) }}</div>
                <div class="stat-label text-muted">{{ __('Completed') }}</div>
            </div>
        </div>
    </div>
</div>

{{-- Filters --}}
<div class="card filter-card mb-4">
    <div class="card-body py-3">
        <form method="GET" action="{{ route('housekeeping.all-work') }}" class="row g-2 align-items-end">
            <div class="col-sm-6 col-md-3">
                <label class="form-label small text-muted mb-1">{{ __('Status') }}</label>
                <select name="status" class="form-select form-select-sm">
                    <option value="">{{ __('All Statuses') }}</option>
                    <option value="pending"     {{ request('status') === 'pending'     ? 'selected' : '' }}>{{ __('Pending') }}</option>
                    <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }}>{{ __('In Progress') }}</option>
                    <option value="completed"   {{ request('status') === 'completed'   ? 'selected' : '' }}>{{ __('Completed') }}</option>
                </select>
            </div>
            <div class="col-sm-6 col-md-3">
                <label class="form-label small text-muted mb-1">{{ __('Housekeeper') }}</label>
                <select name="user_id" class="form-select form-select-sm">
                    <option value="">{{ __('All Housekeepers') }}</option>
                    @foreach($housekeepers as $u)
                        <option value="{{ $u->id }}" {{ request('user_id') == $u->id ? 'selected' : '' }}>{{ $u->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-sm-6 col-md-2">
                <label class="form-label small text-muted mb-1">{{ __('Date From') }}</label>
                <input type="date" name="date_from" class="form-control form-control-sm" value="{{ request('date_from') }}">
            </div>
            <div class="col-sm-6 col-md-2">
                <label class="form-label small text-muted mb-1">{{ __('Date To') }}</label>
                <input type="date" name="date_to" class="form-control form-control-sm" value="{{ request('date_to') }}">
            </div>
            <div class="col-12 col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-primary btn-sm flex-fill">
                    <i class="bi bi-funnel me-1"></i>{{ __('Filter') }}
                </button>
                <a href="{{ route('housekeeping.all-work') }}" class="btn btn-outline-secondary btn-sm flex-fill">
                    <i class="bi bi-x-lg"></i>
                </a>
            </div>
        </form>
    </div>
</div>

{{-- Results count --}}
<div class="d-flex justify-content-between align-items-center mb-2">
    <span class="small text-muted">
        {{ __('Showing') }} <strong>{{ $assignments->firstItem() ?? 0 }}</strong>–<strong>{{ $assignments->lastItem() ?? 0 }}</strong>
        {{ __('of') }} <strong>{{ $assignments->total() }}</strong> {{ __('records') }}
    </span>
    @if(request()->hasAny(['status','user_id','date_from','date_to']))
        <span class="badge bg-warning text-dark">{{ __('Filtered results') }}</span>
    @endif
</div>

{{-- Table --}}
<div class="card border-0 shadow-sm">
    @if($assignments->isEmpty())
        <div class="card-body text-center py-5">
            <i class="bi bi-search fs-1 text-muted opacity-50 d-block mb-3"></i>
            <p class="mb-0 text-muted">{{ __('No housekeeping assignments found.') }}</p>
            @if(request()->hasAny(['status','user_id','date_from','date_to']))
                <a href="{{ route('housekeeping.all-work') }}" class="btn btn-outline-secondary btn-sm mt-3">
                    {{ __('Clear filters') }}
                </a>
            @endif
        </div>
    @else
    <div class="table-responsive">
        <table class="table table-hover mb-0 align-middle">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>{{ __('Date') }}</th>
                    <th>{{ __('Room') }}</th>
                    <th>{{ __('Type') }}</th>
                    <th>{{ __('Assigned To') }}</th>
                    <th>{{ __('Status') }}</th>
                    <th>{{ __('Assigned At') }}</th>
                    <th>{{ __('Completed At') }}</th>
                    <th>{{ __('Notes') }}</th>
                    <th class="text-end">{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($assignments as $a)
                @php
                    $cfg = \App\Models\HousekeepingAssignment::statusBadgeConfig($a->status);
                    $initials = strtoupper(substr($a->assignedTo?->name ?? 'U', 0, 1))
                              . strtoupper(substr(strstr($a->assignedTo?->name ?? ' ', ' '), 1, 1));
                @endphp
                <tr>
                    <td class="text-muted small">{{ ($assignments->currentPage()-1)*$assignments->perPage() + $loop->iteration }}</td>
                    <td>
                        <a href="{{ route('housekeeping.index', ['date' => $a->date->format('Y-m-d')]) }}" class="fw-medium text-decoration-none">
                            {{ $a->date->format('d M Y') }}
                        </a>
                        @if($a->date->isToday())
                            <span class="badge bg-primary ms-1" style="font-size:.65rem;">Today</span>
                        @endif
                    </td>
                    <td>
                        <span class="fw-medium">{{ $a->room?->number ?? '—' }}</span>
                    </td>
                    <td class="text-muted small">{{ $a->room?->roomType?->name ?? '—' }}</td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <div class="avatar-initials">{{ $initials ?: 'U' }}</div>
                            <span class="small">{{ $a->assignedTo?->name ?? '—' }}</span>
                        </div>
                    </td>
                    <td>
                        <span class="badge badge-status {{ $cfg['class'] }}">{{ $cfg['label'] }}</span>
                    </td>
                    <td class="small text-nowrap text-muted">{{ $a->created_at?->format('d M, h:i A') ?? '—' }}</td>
                    <td class="small text-nowrap text-muted">
                        @if($a->completed_at)
                            <span class="text-success">{{ $a->completed_at->format('d M, h:i A') }}</span>
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </td>
                    <td class="small text-muted" style="max-width:160px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;" title="{{ $a->notes }}">
                        {{ $a->notes ?: '—' }}
                    </td>
                    <td class="text-end text-nowrap">
                        @can('housekeeping.assign_others')
                            <a href="{{ route('housekeeping.edit', $a) }}" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="{{ route('housekeeping.destroy', $a) }}" method="POST" class="d-inline"
                                  onsubmit="return confirm('{{ __('Delete this assignment?') }}')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        @endcan
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if($assignments->hasPages())
    <div class="card-footer bg-white border-top-0 d-flex justify-content-between align-items-center py-2 px-3 flex-wrap gap-2">
        <small class="text-muted">
            {{ __('Page') }} {{ $assignments->currentPage() }} {{ __('of') }} {{ $assignments->lastPage() }}
        </small>
        {{ $assignments->links() }}
    </div>
    @endif
    @endif
</div>

@endsection
