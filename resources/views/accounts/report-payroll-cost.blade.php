@extends('layouts.app')
@section('title', 'Payroll Cost Report')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-1 fw-bold">👥 Payroll Cost Report</h1>
        <p class="text-muted small mb-0">Period: <strong>{{ $from->format('d M Y') }}</strong> — <strong>{{ $to->format('d M Y') }}</strong></p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('accounts.reports.payroll-cost.pdf-preview', ['from' => $from->format('Y-m-d'), 'to' => $to->format('Y-m-d')]) }}"
           class="btn btn-outline-primary btn-sm" target="_blank">
            <i class="fas fa-print me-1"></i> Preview PDF
        </a>
        <a href="{{ route('accounts.reports.payroll-cost.pdf', ['from' => $from->format('Y-m-d'), 'to' => $to->format('Y-m-d')]) }}"
           class="btn btn-danger btn-sm">
            <i class="fas fa-file-pdf me-1"></i> Download PDF
        </a>
        <a href="{{ route('accounts.reports.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-arrow-left me-1"></i> Back to Reports
        </a>
    </div>
</div>
<form method="GET" action="{{ route('accounts.reports.payroll-cost') }}" class="card border-0 shadow-sm mb-4 bg-light">
    <div class="card-body py-3">
        <div class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label small fw-bold">From Date</label>
                <input type="date" name="from" class="form-control form-control-sm" value="{{ $from->format('Y-m-d') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-bold">To Date</label>
                <input type="date" name="to" class="form-control form-control-sm" value="{{ $to->format('Y-m-d') }}">
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-primary px-4">
                    <i class="fas fa-filter me-1"></i> Apply Filter
                </button>
            </div>
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
