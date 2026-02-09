@extends('layouts.app')
@section('title', 'POS Reports')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">POS Reports</h1>
    <a href="{{ route('pos.index') }}" class="btn btn-outline-secondary">Back to POS</a>
</div>

<form action="{{ route('pos.reports') }}" method="GET" class="card border-0 shadow-sm mb-4">
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
                <h6 class="text-muted small text-uppercase">Instant payment (collected)</h6>
                <h3 class="mb-0">{{ money($instantTotal) }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <h6 class="text-muted small text-uppercase">Room charge (posted)</h6>
                <h3 class="mb-0">{{ money($roomChargeTotal) }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <h6 class="text-muted small text-uppercase">Total sales</h6>
                <h3 class="mb-0">{{ money($instantTotal + $roomChargeTotal) }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <h6 class="text-muted small text-uppercase">Orders</h6>
                <h3 class="mb-0">{{ $orders->count() }}</h3>
            </div>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-6">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white"><strong>Sales by POS type</strong></div>
            <div class="card-body">
                <table class="table table-sm mb-0">
                    @foreach($byType as $type => $total)
                    <tr>
                        <td>{{ str_replace('_', ' ', ucfirst($type)) }}</td>
                        <td class="text-end">{{ money($total) }}</td>
                    </tr>
                    @endforeach
                    @if($byType->isEmpty())
                    <tr><td class="text-muted">No data in period</td></tr>
                    @endif
                </table>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white"><strong>Payment method summary</strong></div>
            <div class="card-body">
                <table class="table table-sm mb-0">
                    @foreach($paymentMethodSummary as $method => $total)
                    <tr>
                        <td>{{ str_replace('_', ' ', ucfirst($method)) }}</td>
                        <td class="text-end">{{ money($total) }}</td>
                    </tr>
                    @endforeach
                    @if($paymentMethodSummary->isEmpty())
                    <tr><td class="text-muted">No payments in period</td></tr>
                    @endif
                </table>
            </div>
        </div>
    </div>
</div>

<p class="small text-muted">Orders list: <a href="{{ route('pos.orders-list', ['from' => $from->format('Y-m-d'), 'to' => $to->format('Y-m-d'), 'outlet_id' => $outletId]) }}">Orders &amp; Report</a></p>
@endsection
