@extends('layouts.app')
@section('title', 'System Reports')
@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-1 fw-bold">📈 System Reports</h1>
        <p class="text-muted small mb-0">Overview of hotel operations, occupancy, and financial reports.</p>
    </div>
</div>

<div class="row g-4">
    {{-- Operational Dashboard --}}
    <div class="col-md-6">
        <div class="card border-0 shadow-sm h-100" style="border-top: 4px solid #3b82f6 !important;">
            <div class="card-body p-4">
                <div class="d-flex align-items-center mb-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center me-3 flex-shrink-0"
                         style="width:48px;height:48px;background:linear-gradient(135deg,#3b82f6,#2563eb);">
                        <i class="fas fa-tachometer-alt text-white"></i>
                    </div>
                    <div>
                        <h5 class="mb-0 fw-bold">Operations Dashboard</h5>
                        <small class="text-muted">Daily summary, occupancy & revenue alerts</small>
                    </div>
                </div>
                <p class="text-muted small">View the current state of hotel operations including today's occupancy and revenue.</p>
                <a href="{{ route('reports.dashboard') }}" class="btn btn-primary btn-sm w-100 mt-2">
                    <i class="fas fa-eye me-1"></i> Open Dashboard
                </a>
            </div>
        </div>
    </div>

    {{-- Financial Reports (Link to Accounts) --}}
    <div class="col-md-6">
        <div class="card border-0 shadow-sm h-100" style="border-top: 4px solid #059669 !important;">
            <div class="card-body p-4">
                <div class="d-flex align-items-center mb-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center me-3 flex-shrink-0"
                         style="width:48px;height:48px;background:linear-gradient(135deg,#059669,#065f46);">
                        <i class="fas fa-file-invoice-dollar text-white"></i>
                    </div>
                    <div>
                        <h5 class="mb-0 fw-bold">Financial Reports</h5>
                        <small class="text-muted">Profit & Loss, Expense, Cash Summary</small>
                    </div>
                </div>
                <p class="text-muted small">Comprehensive financial statements and account breakdowns with PDF export.</p>
                <a href="{{ route('accounts.reports.index') }}" class="btn btn-sm w-100 mt-2" style="background:#059669;color:#fff;">
                    <i class="fas fa-chart-bar me-1"></i> Go to Finance Reports
                </a>
            </div>
        </div>
    </div>

    {{-- Occupancy Detail --}}
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body p-4 text-center">
                <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                     style="width:60px;height:60px;background:#eff6ff;">
                    <i class="fas fa-bed text-primary fa-lg"></i>
                </div>
                <h6 class="fw-bold mb-2">Occupancy Report</h6>
                <p class="text-muted small mb-3">Detailed list of room occupancy and availability status.</p>
                <a href="{{ route('reports.occupancy') }}" class="btn btn-outline-primary btn-sm px-4">View Detail</a>
            </div>
        </div>
    </div>

    {{-- Revenue Detail --}}
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body p-4 text-center">
                <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                     style="width:60px;height:60px;background:#f0fdf4;">
                    <i class="fas fa-hand-holding-usd text-success fa-lg"></i>
                </div>
                <h6 class="fw-bold mb-2">Revenue Analytics</h6>
                <p class="text-muted small mb-3">Analyze revenue trends across different periods and categories.</p>
                <a href="{{ route('reports.revenue') }}" class="btn btn-outline-success btn-sm px-4">View Analytics</a>
            </div>
        </div>
    </div>

    {{-- POS Reports (Optional Link) --}}
    @if(Route::has('pos.reports'))
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body p-4 text-center">
                <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                     style="width:60px;height:60px;background:#fff7ed;">
                    <i class="fas fa-utensils text-warning fa-lg"></i>
                </div>
                <h6 class="fw-bold mb-2">POS Reports</h6>
                <p class="text-muted small mb-3">Restaurant and Point of Sale transaction summaries.</p>
                <a href="{{ route('pos.reports') }}" class="btn btn-outline-warning btn-sm px-4">POS Reports</a>
            </div>
        </div>
    @endif
</div>

@endsection
