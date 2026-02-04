@extends('layouts.app')
@section('title', 'Employee')
@section('content')
<h1 class="h3 mb-4">{{ $employee->full_name }}</h1>
<dl class="row"><dt class="col-sm-3">Employee #</dt><dd class="col-sm-9">{{ $employee->employee_number }}</dd><dt class="col-sm-3">Department</dt><dd class="col-sm-9">{{ $employee->department ?? '-' }}</dd><dt class="col-sm-3">Designation</dt><dd class="col-sm-9">{{ $employee->designation ?? '-' }}</dd><dt class="col-sm-3">Join date</dt><dd class="col-sm-9">{{ $employee->join_date?->format('Y-m-d') ?? '-' }}</dd><dt class="col-sm-3">Base salary</dt><dd class="col-sm-9">{{ number_format($employee->base_salary, 2) }}</dd></dl>
<a href="{{ route('hr.employees.edit', $employee) }}" class="btn btn-primary">Edit</a>
<a href="{{ route('hr.employees.index') }}" class="btn btn-secondary">Back</a>
@endsection
