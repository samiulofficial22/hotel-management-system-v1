@extends('layouts.app')
@section('title', 'Add Employee')
@section('content')
<h1 class="h3 mb-4">Add Employee</h1>
<form method="POST" action="{{ route('hr.employees.store') }}">
@csrf
<div class="mb-3"><label class="form-label">First name</label><input type="text" name="first_name" class="form-control" required></div>
<div class="mb-3"><label class="form-label">Last name</label><input type="text" name="last_name" class="form-control" required></div>
<div class="mb-3"><label class="form-label">Employee number (optional, auto-generated)</label><input type="text" name="employee_number" class="form-control"></div>
<div class="mb-3"><label class="form-label">Department</label><input type="text" name="department" class="form-control"></div>
<div class="mb-3"><label class="form-label">Designation</label><input type="text" name="designation" class="form-control"></div>
<div class="mb-3"><label class="form-label">Join date</label><input type="date" name="join_date" class="form-control"></div>
<div class="mb-3"><label class="form-label">Base salary</label><input type="number" name="base_salary" class="form-control" step="0.01" min="0" value="0"></div>
<button type="submit" class="btn btn-primary">Create</button>
<a href="{{ route('hr.employees.index') }}" class="btn btn-secondary">Cancel</a>
</form>
@endsection
