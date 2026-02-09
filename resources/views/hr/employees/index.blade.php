@extends('layouts.app')
@section('title', __('messages.Employees'))
@section('content')
<div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
    <h1 class="h3 mb-0">{{ __('messages.Employees') }}</h1>
    <div class="d-flex gap-2">
        <form action="{{ route('hr.employees.sync') }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('Create/update office staff users from all employees?') }}');">
            @csrf
            <button type="submit" class="btn btn-outline-primary">{{ __('Sync to Office Staff') }}</button>
        </form>
        <a href="{{ route('hr.employees.create') }}" class="btn btn-primary">{{ __('Add Employee') }}</a>
    </div>
</div>

@if(session('sync_result'))
    <div class="alert alert-info alert-dismissible fade show">
        {{ session('sync_result') }}
        @if(session('sync_errors') && count(session('sync_errors')) > 0)
            <ul class="mb-0 mt-2 small">@foreach(session('sync_errors') as $err)<li>{{ $err }}</li>@endforeach</ul>
        @endif
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="{{ __('messages.Close') }}"></button>
    </div>
@endif

<table class="table table-striped align-middle">
    <thead>
        <tr>
            <th>ID</th>
            <th></th>
            <th>{{ __('messages.Name') }}</th>
            <th>{{ __('Employee code') }}</th>
            <th>{{ __('messages.Department') }}</th>
            <th>{{ __('Designation') }}</th>
            <th>{{ __('Join date') }}</th>
            <th>{{ __('User') }}</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        @foreach($employees as $e)
        <tr>
            <td>{{ ($employees->currentPage() - 1) * $employees->perPage() + $loop->iteration }}</td>
            <td>
                <img src="{{ $e->photo_url }}" alt="" class="rounded-circle" width="36" height="36" style="object-fit: cover;">
            </td>
            <td>{{ $e->display_name }}</td>
            <td>{{ $e->employee_code ?? $e->employee_number }}</td>
            <td>{{ $e->departmentRelation?->name ?? $e->department ?? '-' }}</td>
            <td>{{ $e->designation ?? '-' }}</td>
            <td>{{ $e->join_date?->format('Y-m-d') ?? '-' }}</td>
            <td>@if($e->user_id)<a href="{{ route('users.edit', $e->user) }}">{{ $e->user->name ?? $e->user->email }}</a>@else<span class="text-muted">-</span>@endif</td>
            <td>
                <a href="{{ route('hr.employees.show', $e) }}">{{ __('messages.View') }}</a>
                <a href="{{ route('hr.employees.edit', $e) }}" class="ms-2">{{ __('Edit') }}</a>
                <form action="{{ route('hr.employees.destroy', $e) }}" method="POST" class="d-inline ms-2" onsubmit="return confirm('{{ __('Delete this employee? Attendance and payroll records for this employee will also be removed.') }}');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-link btn-sm text-danger p-0 border-0">{{ __('Delete') }}</button>
                </form>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
{{ $employees->links() }}
@endsection
