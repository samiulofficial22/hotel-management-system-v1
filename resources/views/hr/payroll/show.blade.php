@extends('layouts.app')
@section('title', 'Payroll Run')
@section('content')
<h1 class="h3 mb-4">Payroll Run</h1>
<p>Period: {{ $run->period_start->format('Y-m-d') }} to {{ $run->period_end->format('Y-m-d') }} | Status: {{ $run->status }}</p>
@can('payroll.manage')
@if($run->status === 'draft')
<form action="{{ route('hr.payroll.process', $run) }}" method="POST" class="mb-3">@csrf<button type="submit" class="btn btn-warning">Process</button></form>
@endif
@endcan
<table class="table table-striped">
<thead><tr><th>Employee</th><th>Base</th><th>Allowances</th><th>Deductions</th><th>Net</th></tr></thead>
<tbody>
@foreach($run->items as $item)
<tr><td>{{ $item->employee->full_name ?? '-' }}</td><td>{{ number_format($item->base_salary, 2) }}</td><td>{{ number_format($item->allowances, 2) }}</td><td>{{ number_format($item->deductions, 2) }}</td><td>{{ number_format($item->net_salary, 2) }}</td></tr>
@endforeach
</tbody>
</table>
<a href="{{ route('hr.payroll.index') }}" class="btn btn-secondary">Back</a>
@endsection
