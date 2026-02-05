@extends('layouts.app')
@section('title', __('messages.Employees'))
@section('content')
<h1 class="h3 mb-4">{{ __('messages.Employees') }}</h1>
<p><a href="{{ route('hr.employees.create') }}" class="btn btn-primary">{{ __('Add Employee') }}</a></p>

<table class="table table-striped align-middle">
    <thead>
        <tr>
            <th></th>
            <th>{{ __('messages.Name') }}</th>
            <th>{{ __('Employee code') }}</th>
            <th>{{ __('messages.Department') }}</th>
            <th>{{ __('Designation') }}</th>
            <th>{{ __('Join date') }}</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        @foreach($employees as $e)
        <tr>
            <td>
                <img src="{{ $e->photo_url }}" alt="" class="rounded-circle" width="36" height="36" style="object-fit: cover;">
            </td>
            <td>{{ $e->display_name }}</td>
            <td>{{ $e->employee_code ?? $e->employee_number }}</td>
            <td>{{ $e->departmentRelation?->name ?? $e->department ?? '-' }}</td>
            <td>{{ $e->designation ?? '-' }}</td>
            <td>{{ $e->join_date?->format('Y-m-d') ?? '-' }}</td>
            <td>
                <a href="{{ route('hr.employees.show', $e) }}">{{ __('messages.View') }}</a>
                <a href="{{ route('hr.employees.edit', $e) }}" class="ms-2">{{ __('Edit') }}</a>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
{{ $employees->links() }}
@endsection
