@extends('layouts.app')
@section('title', 'Edit Employee')
@section('content')
<h1 class="h3 mb-4">Edit: {{ $employee->full_name }}</h1>
<form method="POST" action="{{ route('hr.employees.update', $employee) }}">
@csrf
@method('PUT')
<div class="mb-3"><label class="form-label">First name</label><input type="text" name="first_name" class="form-control" value="{{ old('first_name', $employee->first_name) }}" required></div>
<div class="mb-3"><label class="form-label">Last name</label><input type="text" name="last_name" class="form-control" value="{{ old('last_name', $employee->last_name) }}" required></div>
<div class="mb-3"><label class="form-label">Department</label><input type="text" name="department" class="form-control" value="{{ old('department', $employee->department) }}"></div>
<div class="mb-3"><label class="form-label">Designation</label><input type="text" name="designation" class="form-control" value="{{ old('designation', $employee->designation) }}"></div>
<div class="mb-3"><label class="form-label">Base salary</label><input type="number" name="base_salary" class="form-control" step="0.01" min="0" value="{{ old('base_salary', $employee->base_salary) }}"></div>
<button type="submit" class="btn btn-primary">Update</button>
<a href="{{ route('hr.employees.show', $employee) }}" class="btn btn-secondary">Cancel</a>
</form>
@endsection
