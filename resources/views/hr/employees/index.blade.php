@extends('layouts.app')
@section('title', 'Employees')
@section('content')
<h1 class="h3 mb-4">Employees</h1>
<p><a href="{{ route('hr.employees.create') }}" class="btn btn-primary">Add Employee</a></p>
<table class="table table-striped">
<thead><tr><th>#</th><th>Name</th><th>Department</th><th>Designation</th><th>Join Date</th><th></th></tr></thead>
<tbody>
@foreach($employees as $e)
<tr><td>{{ $e->employee_number }}</td><td>{{ $e->full_name }}</td><td>{{ $e->department ?? '-' }}</td><td>{{ $e->designation ?? '-' }}</td><td>{{ $e->join_date?->format('Y-m-d') ?? '-' }}</td><td><a href="{{ route('hr.employees.show', $e) }}">View</a> <a href="{{ route('hr.employees.edit', $e) }}">Edit</a></td></tr>
@endforeach
</tbody>
</table>
{{ $employees->links() }}
@endsection
