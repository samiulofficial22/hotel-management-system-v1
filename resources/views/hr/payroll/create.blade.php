@extends('layouts.app')
@section('title', 'New Payroll Run')
@section('content')
<h1 class="h3 mb-4">New Payroll Run</h1>
<form method="POST" action="{{ route('hr.payroll.store') }}">
@csrf
<div class="mb-3"><label class="form-label">Period start</label><input type="date" name="period_start" class="form-control" required></div>
<div class="mb-3"><label class="form-label">Period end</label><input type="date" name="period_end" class="form-control" required></div>
<button type="submit" class="btn btn-primary">Create</button>
<a href="{{ route('hr.payroll.index') }}" class="btn btn-secondary">Cancel</a>
</form>
@endsection
