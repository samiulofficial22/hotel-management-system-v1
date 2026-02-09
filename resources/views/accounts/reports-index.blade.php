@extends('layouts.app')
@section('title', 'Accounts Reports')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Accounts Reports</h1>
    <a href="{{ route('accounts.index') }}" class="btn btn-outline-secondary">Chart of Accounts</a>
</div>
<div class="row g-3">
    <div class="col-md-6 col-lg-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <h5 class="card-title">Profit & Loss</h5>
                <p class="card-text small text-muted">Revenue minus expenses for a date range.</p>
                <a href="{{ route('accounts.reports.profit-loss') }}" class="btn btn-primary">View P&L</a>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-lg-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <h5 class="card-title">Expense Report</h5>
                <p class="card-text small text-muted">Expense breakdown by account.</p>
                <a href="{{ route('accounts.reports.expense') }}" class="btn btn-primary">View Expense</a>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-lg-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <h5 class="card-title">Daily Cash Summary</h5>
                <p class="card-text small text-muted">Daily cash movements.</p>
                <a href="{{ route('accounts.reports.daily-cash') }}" class="btn btn-primary">View Daily Cash</a>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-lg-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <h5 class="card-title">Payroll Cost Report</h5>
                <p class="card-text small text-muted">Payroll runs paid in period.</p>
                <a href="{{ route('accounts.reports.payroll-cost') }}" class="btn btn-primary">View Payroll Cost</a>
            </div>
        </div>
    </div>
</div>
@endsection
