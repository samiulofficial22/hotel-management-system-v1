@extends('layouts.app')
@section('title', 'Payroll Cost Report')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Payroll Cost Report</h1>
    <a href="{{ route('accounts.reports.index') }}" class="btn btn-outline-secondary">Back to Reports</a>
</div>
<form method="GET" action="{{ route('accounts.reports.payroll-cost') }}" class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <div class="row g-2 align-items-end">
            <div class="col-auto"><label class="form-label small">From</label><input type="date" name="from" class="form-control form-control-sm" value="{{ $from->format('Y-m-d') }}"></div>
            <div class="col-auto"><label class="form-label small">To</label><input type="date" name="to" class="form-control form-control-sm" value="{{ $to->format('Y-m-d') }}"></div>
            <div class="col-auto"><button type="submit" class="btn btn-primary btn-sm">Apply</button></div>
        </div>
    </div>
</form>
<div class="card border-0 shadow-sm">
    <div class="card-body">
        <table class="table table-striped mb-0">
            <thead><tr><th>Payroll</th><th>Period</th><th>Paid at</th><th class="text-end">Total</th></tr></thead>
            <tbody>
                @foreach($runs as $run)
                <tr>
                    <td>{{ $run->title }}</td>
                    <td>{{ $run->period_start->format('Y-m-d') }} to {{ $run->period_end->format('Y-m-d') }}</td>
                    <td>{{ $run->paid_at?->format('Y-m-d H:i') }}</td>
                    <td class="text-end">{{ money($run->items->sum('net_salary')) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @if($runs->isEmpty())<p class="text-muted mb-0">No payroll paid in this period.</p>@endif
        <p class="mb-0 mt-2 fw-bold">Total Payroll Cost: {{ money($totalCost) }}</p>
    </div>
</div>
@endsection
