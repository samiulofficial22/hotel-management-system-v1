@extends('layouts.app')
@section('title', 'POS')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">POS – Select Outlet</h1>
    <div>
        @can('pos.view_reports')
        <a href="{{ route('pos.reports') }}" class="btn btn-outline-primary me-2">POS Reports</a>
        @endcan
        <a href="{{ route('pos.orders-list') }}" class="btn btn-outline-primary">Orders & Report</a>
    </div>
</div>
@if($outlets->isEmpty())
    <div class="alert alert-info">No outlets yet. <a href="{{ route('outlets.create') }}">Create an outlet</a> first, then add menu categories and items.</div>
@else
    <div class="row g-3">
        @foreach($outlets as $outlet)
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex flex-column">
                    <h5 class="card-title">{{ $outlet->name }}</h5>
                    <p class="card-text text-muted small">{{ ucfirst($outlet->type ?? '') }}</p>
                    <a href="{{ route('pos.outlet', $outlet->id) }}" class="btn btn-primary mt-auto">Open POS</a>
                </div>
            </div>
        </div>
        @endforeach
    </div>
@endif
@endsection
