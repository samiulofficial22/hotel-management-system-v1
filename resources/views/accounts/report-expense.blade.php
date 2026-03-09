@extends('layouts.app')
@section('title', 'Expense Report')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-1 fw-bold">🧾 Expense Report</h1>
        <p class="text-muted small mb-0">Period: <strong>{{ $from->format('d M Y') }}</strong> — <strong>{{ $to->format('d M Y') }}</strong></p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('accounts.reports.expense.pdf', ['from' => $from->format('Y-m-d'), 'to' => $to->format('Y-m-d')]) }}"
           class="btn btn-danger btn-sm" target="_blank">
            <i class="fas fa-file-pdf me-1"></i> Download PDF
        </a>
        <a href="{{ route('accounts.reports.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-arrow-left me-1"></i> Back to Reports
        </a>
    </div>
</div>
<form method="GET" action="{{ route('accounts.reports.expense') }}" class="card border-0 shadow-sm mb-4 bg-light">
    <div class="card-body py-3">
        <div class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label small fw-bold">From Date</label>
                <input type="date" name="from" class="form-control" value="{{ $from->format('Y-m-d') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-bold">To Date</label>
                <input type="date" name="to" class="form-control" value="{{ $to->format('Y-m-d') }}">
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
            <thead><tr><th>Code</th><th>Account</th><th class="text-end">Amount</th></tr></thead>
            <tbody>
                @foreach($breakdown as $row)
                <tr><td>{{ $row['code'] }}</td><td>{{ $row['name'] }}</td><td class="text-end">{{ money($row['amount']) }}</td></tr>
                @endforeach
            </tbody>
        </table>
        @if(empty($breakdown))<p class="text-muted mb-0">No expense in this period.</p>@endif
        <p class="mb-0 mt-2 fw-bold">Total: {{ money($total) }}</p>
    </div>
</div>
@endsection
