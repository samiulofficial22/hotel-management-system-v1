@extends('layouts.app')
@section('title', __('Attendance'))
@section('content')
<div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
    <h1 class="h3 mb-0">Employee Attendance</h1>
    <a href="{{ route('hr.employees.index') }}" class="btn btn-outline-secondary btn-sm">Employees</a>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show py-2" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close btn-sm" data-bs-dismiss="alert"></button>
    </div>
@endif

{{-- Date navigation --}}
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body py-3">
        <form method="GET" action="{{ route('hr.attendance.index') }}" class="row g-2 align-items-center flex-wrap">
            <div class="col-auto">
                <a href="{{ route('hr.attendance.index', ['date' => $date->copy()->subDay()->format('Y-m-d')]) }}" class="btn btn-outline-secondary btn-sm" title="Previous day">← Prev</a>
            </div>
            <div class="col-auto d-flex align-items-center gap-2">
                <label class="form-label mb-0 small text-muted">Date</label>
                <input type="date" name="date" class="form-control form-control-sm" value="{{ $date->format('Y-m-d') }}" style="width:150px">
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-primary btn-sm">Go</button>
            </div>
            <div class="col-auto">
                <a href="{{ route('hr.attendance.index', ['date' => $date->copy()->addDay()->format('Y-m-d')]) }}" class="btn btn-outline-secondary btn-sm" title="Next day">Next →</a>
            </div>
            @if(!$date->isToday())
            <div class="col-auto">
                <a href="{{ route('hr.attendance.index') }}" class="btn btn-outline-primary btn-sm">Today</a>
            </div>
            @endif
        </form>
    </div>
</div>

@php
    $presentCount = $attendances->where('status', 'present')->count();
    $absentCount = $attendances->where('status', 'absent')->count();
    $halfDayCount = $attendances->where('status', 'half_day')->count();
    $leaveCount = $attendances->where('status', 'leave')->count();
    $notMarkedCount = $employees->count() - $attendances->count();
@endphp

{{-- Summary --}}
<div class="row g-2 mb-4">
    <div class="col-6 col-md">
        <div class="card border-0 shadow-sm border-start border-success border-3">
            <div class="card-body py-2 px-3">
                <span class="small text-muted">Present</span>
                <h5 class="mb-0 text-success">{{ $presentCount }}</h5>
            </div>
        </div>
    </div>
    <div class="col-6 col-md">
        <div class="card border-0 shadow-sm border-start border-danger border-3">
            <div class="card-body py-2 px-3">
                <span class="small text-muted">Absent</span>
                <h5 class="mb-0 text-danger">{{ $absentCount }}</h5>
            </div>
        </div>
    </div>
    <div class="col-6 col-md">
        <div class="card border-0 shadow-sm border-start border-warning border-3">
            <div class="card-body py-2 px-3">
                <span class="small text-muted">Half day</span>
                <h5 class="mb-0 text-warning">{{ $halfDayCount }}</h5>
            </div>
        </div>
    </div>
    <div class="col-6 col-md">
        <div class="card border-0 shadow-sm border-start border-info border-3">
            <div class="card-body py-2 px-3">
                <span class="small text-muted">Leave</span>
                <h5 class="mb-0 text-info">{{ $leaveCount }}</h5>
            </div>
        </div>
    </div>
    <div class="col-6 col-md">
        <div class="card border-0 shadow-sm border-start border-secondary border-3">
            <div class="card-body py-2 px-3">
                <span class="small text-muted">Not marked</span>
                <h5 class="mb-0 text-secondary">{{ $notMarkedCount }}</h5>
            </div>
        </div>
    </div>
</div>

{{-- Attendance sheet: all employees --}}
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3">
        <strong>Attendance sheet — {{ $date->format('l, d M Y') }}</strong>
    </div>
    <div class="card-body p-0">
        @if($employees->isEmpty())
            <p class="text-muted p-4 mb-0">No active employees. <a href="{{ route('hr.employees.create') }}">Add employees</a> first.</p>
        @else
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-3">#</th>
                            <th>Employee</th>
                            <th>Check in</th>
                            <th>Check out</th>
                            <th>Overtime (Hrs)</th>
                            <th>Status</th>
                            @can('hr.manage')<th class="text-end pe-3">Action</th>@endcan
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($employees as $index => $e)
                            @php $att = $attendancesByEmployee->get($e->id); @endphp
                            <tr>
                                <td class="ps-3">{{ $index + 1 }}</td>
                                <td>
                                    <span class="fw-medium">{{ $e->full_name }}</span>
                                    <div class="small text-muted">{{ $e->departmentRelation->name ?? $e->department ?? 'N/A' }}</div>
                                </td>
                                @can('hr.manage')
                                <form method="POST" action="{{ route('hr.attendance.mark') }}" style="display: contents;">
                                    @csrf
                                    <input type="hidden" name="date" value="{{ $date->format('Y-m-d') }}">
                                    <input type="hidden" name="employee_id" value="{{ $e->id }}">
                                    <td>
                                        <input type="time" name="check_in" class="form-control form-control-sm" value="{{ $att && $att->check_in ? \Carbon\Carbon::parse($att->check_in)->format('H:i') : '' }}" style="width:100px">
                                    </td>
                                    <td>
                                        <input type="time" name="check_out" class="form-control form-control-sm" value="{{ $att && $att->check_out ? \Carbon\Carbon::parse($att->check_out)->format('H:i') : '' }}" style="width:100px">
                                    </td>
                                    <td>
                                        <input type="number" step="0.5" min="0" name="overtime_hours" class="form-control form-control-sm" value="{{ $att ? ($att->overtime_hours + 0) : '' }}" style="width:80px" placeholder="0">
                                    </td>
                                    <td>
                                        <select name="status" class="form-select form-select-sm" style="width:110px">
                                            <option value="present" {{ ($att->status ?? '') === 'present' ? 'selected' : '' }}>Present</option>
                                            <option value="absent" {{ ($att->status ?? '') === 'absent' ? 'selected' : '' }}>Absent</option>
                                            <option value="half_day" {{ ($att->status ?? '') === 'half_day' ? 'selected' : '' }}>Half day</option>
                                            <option value="leave" {{ ($att->status ?? '') === 'leave' ? 'selected' : '' }}>Leave</option>
                                        </select>
                                    </td>
                                    <td class="pe-3 text-end">
                                        <button type="submit" class="btn btn-sm btn-primary">Save</button>
                                    </td>
                                </form>
                                @else
                                <td>{{ $att && $att->check_in ? \Carbon\Carbon::parse($att->check_in)->format('h:i A') : '–' }}</td>
                                <td>{{ $att && $att->check_out ? \Carbon\Carbon::parse($att->check_out)->format('h:i A') : '–' }}</td>
                                <td>{{ $att && $att->overtime_hours ? ($att->overtime_hours + 0) : '–' }}</td>
                                <td>
                                    @if($att)
                                        @if($att->status === 'present')<span class="badge bg-success">Present</span>
                                        @elseif($att->status === 'absent')<span class="badge bg-danger">Absent</span>
                                        @elseif($att->status === 'half_day')<span class="badge bg-warning text-dark">Half day</span>
                                        @elseif($att->status === 'leave')<span class="badge bg-info">Leave</span>
                                        @else<span class="badge bg-secondary">{{ $att->status }}</span>@endif
                                    @else
                                        <span class="text-muted">–</span>
                                    @endif
                                </td>
                                @endcan
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>

<p class="small text-muted mt-3 mb-0">@can('hr.manage')Mark check-in/check-out time and status per employee, then click Save for that row.@else View-only: you cannot edit attendance.@endcan</p>
@endsection
