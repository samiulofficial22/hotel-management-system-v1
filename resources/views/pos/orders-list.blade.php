@extends('layouts.app')
@section('title', 'POS – Orders & Report')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">POS – Orders & Report</h1>
    <a href="{{ route('pos.index') }}" class="btn btn-outline-secondary">Back to POS</a>
</div>

<div class="alert alert-info py-2 mb-3 small">
    <strong>How cost tracking works:</strong> Set <strong>Cost</strong> (unit cost) for each menu item under Outlets → select outlet → Menu Items → Add/Edit item. When you add that item to an order, its cost is saved. Here you see <strong>Revenue</strong> (sales), <strong>Total Cost</strong>, and <strong>Profit/Loss</strong> for completed orders in the selected period.
</div>

<form action="{{ route('pos.orders-list') }}" method="GET" class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <div class="row g-2 align-items-end">
            <div class="col-md-2">
                <label class="form-label small text-muted">From</label>
                <input type="date" name="from" class="form-control form-control-sm" value="{{ $from->format('Y-m-d') }}">
            </div>
            <div class="col-md-2">
                <label class="form-label small text-muted">To</label>
                <input type="date" name="to" class="form-control form-control-sm" value="{{ $to->format('Y-m-d') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label small text-muted">Outlet</label>
                <select name="outlet_id" class="form-select form-select-sm">
                    <option value="">All outlets</option>
                    @foreach($outlets as $o)
                    <option value="{{ $o->id }}" {{ $outletId == $o->id ? 'selected' : '' }}>{{ $o->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary btn-sm w-100">Apply</button>
            </div>
        </div>
    </div>
</form>

<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <h6 class="text-muted small text-uppercase">Total Orders</h6>
                <h3 class="mb-0">{{ $orders->count() }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <h6 class="text-muted small text-uppercase">Revenue (Sales)</h6>
                <h3 class="mb-0">{{ number_format($revenue, 2) }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <h6 class="text-muted small text-uppercase">Total Cost</h6>
                <h3 class="mb-0">{{ number_format($cost, 2) }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <h6 class="text-muted small text-uppercase">Profit / Loss</h6>
                <h3 class="mb-0 {{ $profit >= 0 ? 'text-success' : 'text-danger' }}">{{ number_format($profit, 2) }}</h3>
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white"><strong>Completed Orders List</strong></div>
    <div class="table-responsive">
        <table class="table table-striped mb-0">
            <thead><tr><th>Order #</th><th>Outlet</th><th>Completed At</th><th>Total</th><th></th></tr></thead>
            <tbody>
                @forelse($orders as $ord)
                <tr>
                    <td>{{ $ord->order_number }}</td>
                    <td>{{ $ord->outlet->name ?? '-' }}</td>
                    <td>{{ $ord->completed_at?->format('Y-m-d H:i') ?? '-' }}</td>
                    <td>{{ number_format($ord->total, 2) }}</td>
                    <td><a href="{{ route('pos.order', $ord->id) }}" class="btn btn-sm btn-outline-primary">View</a></td>
                </tr>
                @empty
                <tr><td colspan="5" class="text-muted text-center py-4">No completed orders in this period.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
