@extends('layouts.app')
@section('title', 'Attendance')
@section('content')
<h1 class="h3 mb-4">Attendance</h1>
<form method="GET" action="{{ route('hr.attendance.index') }}" class="mb-3"><input type="date" name="date" value="{{ $date->format('Y-m-d') }}"><button type="submit" class="btn btn-primary">Go</button></form>
<table class="table table-striped">
<thead><tr><th>Employee</th><th>Check in</th><th>Check out</th><th>Status</th></tr></thead>
<tbody>
@foreach($attendances as $a)
<tr><td>{{ $a->employee->full_name ?? '-' }}</td><td>{{ $a->check_in ?? '-' }}</td><td>{{ $a->check_out ?? '-' }}</td><td>{{ $a->status }}</td></tr>
@endforeach
</tbody>
</table>
@can('hr.manage')
<form method="POST" action="{{ route('hr.attendance.mark') }}" class="mt-3">@csrf<input type="hidden" name="date" value="{{ $date->format('Y-m-d') }}"><select name="employee_id" required>@foreach($employees as $e)<option value="{{ $e->id }}">{{ $e->full_name }}</option>@endforeach</select><input type="time" name="check_in"><input type="time" name="check_out"><select name="status"><option value="present">Present</option><option value="absent">Absent</option></select><button type="submit" class="btn btn-primary">Save</button></form>
@endcan
@endsection
