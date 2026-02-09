@extends('layouts.app')
@section('title', __('Kitchen Display'))
@push('head')
<meta http-equiv="refresh" content="45">
@endpush
@section('content')
<div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
    <h1 class="h3 mb-0">{{ __('Kitchen Display') }}</h1>
    <div class="d-flex align-items-center gap-2">
        <span class="text-muted small">{{ __('Pending orders by outlet') }}</span>
        <a href="{{ route('kitchen.index') }}" class="btn btn-outline-secondary btn-sm" title="{{ __('Refresh') }}">↻ {{ __('Refresh') }}</a>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if($orders->isEmpty())
    <div class="card border-0 shadow-sm">
        <div class="card-body text-center py-5">
            <p class="text-muted mb-0 fs-5">{{ __('No pending orders for kitchen.') }}</p>
        </div>
    </div>
@else
    @php
        $ordersByOutlet = $orders->groupBy('outlet_id');
    @endphp
    @foreach($ordersByOutlet as $outletId => $outletOrders)
        @php
            $outlet = $outletOrders->first()->outlet;
        @endphp
        <div class="mb-4">
            <div class="d-flex align-items-center gap-2 mb-3 pb-2 border-bottom border-2 border-primary">
                <span class="badge bg-primary fs-6 px-3 py-2">{{ $outlet->name ?? __('Outlet') }}</span>
                <span class="text-muted">{{ $outletOrders->count() }} {{ $outletOrders->count() === 1 ? __('order') : __('orders') }}</span>
            </div>
            <div class="row g-3">
                @foreach($outletOrders as $order)
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="card h-100 border shadow-sm kitchen-order-card">
                        <div class="card-header bg-light d-flex flex-wrap justify-content-between align-items-center gap-2 py-3">
                            <div>
                                <strong class="fs-5">{{ $order->order_number }}</strong>
                                @if($order->posTable)
                                    <span class="ms-2 badge bg-secondary">{{ $order->posTable->name }}</span>
                                @endif
                            </div>
                            <span class="text-muted small" title="{{ $order->created_at->format('M d, h:i A') }}">
                                {{ $order->created_at->diffForHumans() }}
                            </span>
                        </div>
                        <div class="card-body p-0">
                            <ul class="list-group list-group-flush">
                                @foreach($order->items as $item)
                                @if(!in_array($item->status, ['ready']))
                                <li class="list-group-item d-flex justify-content-between align-items-center py-3">
                                    <div class="flex-grow-1">
                                        <span class="fw-semibold">{{ $item->menuItem->name ?? '-' }}</span>
                                        <span class="badge bg-light text-dark ms-2">× {{ $item->quantity }}</span>
                                        @if($item->notes)
                                            <div class="small text-muted mt-1">{{ $item->notes }}</div>
                                        @endif
                                    </div>
                                    <form action="{{ route('kitchen.item.ready', $item) }}" method="POST" class="ms-2 flex-shrink-0">
                                        @csrf
                                        <button type="submit" class="btn btn-success btn-sm px-3">{{ __('Ready') }}</button>
                                    </form>
                                </li>
                                @endif
                                @endforeach
                            </ul>
                            @if($order->items->every(fn($i) => $i->status === 'ready'))
                                <div class="p-3 bg-success bg-opacity-10 border-top">
                                    <span class="text-success fw-semibold">✓ {{ __('All items ready') }}</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    @endforeach
@endif
@endsection
