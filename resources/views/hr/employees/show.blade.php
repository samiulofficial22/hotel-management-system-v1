@extends('layouts.app')
@section('title', __('Employee'))
@section('content')
<h1 class="h3 mb-4">{{ $employee->display_name }}</h1>

<div class="d-flex align-items-start gap-4 mb-4">
    <img src="{{ $employee->photo_url }}" alt="" class="rounded-circle flex-shrink-0" width="80" height="80" style="object-fit: cover;">
    <dl class="row mb-0 flex-grow-1">
        <dt class="col-sm-3">{{ __('Employee code') }}</dt>
        <dd class="col-sm-9">{{ $employee->employee_code ?? $employee->employee_number }}</dd>
        <dt class="col-sm-3">{{ __('messages.Department') }}</dt>
        <dd class="col-sm-9">{{ $employee->departmentRelation?->name ?? $employee->department ?? __('messages.Not assigned') }}</dd>
        <dt class="col-sm-3">{{ __('Designation') }}</dt>
        <dd class="col-sm-9">{{ $employee->designation ?? '-' }}</dd>
        <dt class="col-sm-3">{{ __('Join date') }}</dt>
        <dd class="col-sm-9">{{ $employee->join_date?->format('Y-m-d') ?? '-' }}</dd>
        <dt class="col-sm-3">{{ __('Phone') }}</dt>
        <dd class="col-sm-9">{{ $employee->phone ?? '-' }}</dd>
        <dt class="col-sm-3">{{ __('Email') }}</dt>
        <dd class="col-sm-9">{{ $employee->email ?? '-' }}</dd>
        <dt class="col-sm-3">{{ __('Status') }}</dt>
        <dd class="col-sm-9">{{ $employee->status ?? ($employee->is_active ? __('Active') : __('Inactive')) }}</dd>
        <dt class="col-sm-3">{{ __('Employment type') }}</dt>
        <dd class="col-sm-9">{{ $employee->employment_type ?? '-' }}</dd>
        <dt class="col-sm-3">{{ __('Shift') }}</dt>
        <dd class="col-sm-9">{{ $employee->shift ?? '-' }}</dd>
        <dt class="col-sm-3">{{ __('Salary') }}</dt>
        <dd class="col-sm-9">{{ $employee->salary !== null ? number_format($employee->salary, 2) : ($employee->base_salary !== null ? number_format($employee->base_salary, 2) : '-') }}</dd>
        <dt class="col-sm-3">{{ __('NID number') }}</dt>
        <dd class="col-sm-9">{{ $employee->nid_number ?? '-' }}</dd>
        @if($employee->nid_photo_url)
        <dt class="col-sm-3">{{ __('NID photo') }}</dt>
        <dd class="col-sm-9">
            <a href="{{ $employee->nid_photo_url }}" target="_blank" rel="noopener noreferrer">
                <img src="{{ $employee->nid_photo_url }}" alt="NID" class="img-thumbnail" style="max-height: 150px;">
            </a>
            <small class="d-block text-muted"><a href="{{ $employee->nid_photo_url }}" target="_blank" rel="noopener noreferrer">{{ __('Open full size') }}</a></small>
        </dd>
        @endif
    </dl>
</div>

<a href="{{ route('hr.employees.edit', $employee) }}" class="btn btn-primary">{{ __('Edit') }}</a>
<a href="{{ route('hr.employees.index') }}" class="btn btn-secondary">{{ __('Back') }}</a>
@endsection
