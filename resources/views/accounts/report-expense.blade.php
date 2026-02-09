@extends('layouts.app')
@section('title', 'Expense Report')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Expense Report</h1>
    <a href="{{ route('accounts.reports.index') }}" class="btn btn-outline-secondary">Back to Reports</a>
</div>
<form method="GET" action="{{ route('accounts.reports.expense') }}" class="card border-0 shadow-sm mb-4">
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
