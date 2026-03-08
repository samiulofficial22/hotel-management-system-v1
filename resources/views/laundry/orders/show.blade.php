@extends('layouts.app')
@section('title', 'Order #' . $order->id)
@section('content')
<div class="mb-4 d-flex justify-content-between align-items-center">
    <div>
        <a href="{{ route('laundry.orders.index') }}" class="btn btn-link text-decoration-none p-0">
            <i class="bi bi-arrow-left"></i> Back to Orders
        </a>
        <h1 class="h3 mb-0 mt-2">Order #{{ $order->id }}</h1>
    </div>
    <div class="dropdown">
        <button class="btn btn-outline-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
            Update Status
        </button>
        <ul class="dropdown-menu dropdown-menu-end">
            <li>
                <form action="{{ route('laundry.orders.update-status', $order) }}" method="POST">
                    @csrf
                    <button type="submit" name="status" value="processing" class="dropdown-item">Processing</button>
                    <button type="submit" name="status" value="ready" class="dropdown-item">Ready</button>
                    <button type="submit" name="status" value="delivered" class="dropdown-item text-success fw-bold">Delivered</button>
                    <button type="submit" name="status" value="cancelled" class="dropdown-item text-danger">Cancelled</button>
                </form>
            </li>
            @if($order->payment_status !== 'paid')
                <li><hr class="dropdown-divider"></li>
                <li>
                    <form action="{{ route('laundry.orders.update-payment', $order) }}" method="POST">
                        @csrf
                        <input type="hidden" name="payment_status" value="paid">
                        <button type="submit" class="dropdown-item text-success">Mark as Paid</button>
                    </form>
                </li>
            @else
                <li><hr class="dropdown-divider"></li>
                <li>
                    <form action="{{ route('laundry.orders.update-payment', $order) }}" method="POST">
                        @csrf
                        <input type="hidden" name="payment_status" value="unpaid">
                        <button type="submit" class="dropdown-item text-warning">Mark as Unpaid</button>
                    </form>
                </li>
            @endif
        </ul>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-bottom py-3">
                <h5 class="card-title mb-0">Items Summary</h5>
            </div>
            <div class="card-body p-0">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4">Item</th>
                            <th>Service</th>
                            <th>Qty</th>
                            <th>Price</th>
                            <th class="text-end pe-4">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($order->items as $item)
                        <tr>
                            <td class="ps-4 fw-bold">{{ $item->item->name }}</td>
                            <td><span class="badge bg-light text-dark border text-uppercase">{{ str_replace('_', ' ', $item->service_type) }}</span></td>
                            <td>{{ $item->quantity }}</td>
                            <td>{{ money($item->unit_price) }}</td>
                            <td class="text-end pe-4">{{ money($item->subtotal) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="border-top-0">
                        <tr>
                            <td colspan="4" class="text-end fw-bold py-3 ps-4">Total Amount</td>
                            <td class="text-end fw-bold h5 mb-0 py-3 pe-4 text-primary">{{ money($order->total_amount) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-bottom py-3">
                <h5 class="card-title mb-0">Room & Guest</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="text-muted small d-block">Room Number</label>
                    <span class="h5 mb-0">{{ $order->room->number }}</span>
                </div>
                <div class="mb-3">
                    <label class="text-muted small d-block">Order Date & Time</label>
                    <span class="h6 mb-0">{{ $order->order_date ? date('d M, Y', strtotime($order->order_date)) : 'N/A' }} at {{ $order->order_time ? date('h:i A', strtotime($order->order_time)) : '' }}</span>
                </div>
                <div class="mb-3">
                    <label class="text-muted small d-block">Guest Name</label>
                    <span class="h5 mb-0">{{ $order->guest->full_name ?? 'N/A' }}</span>
                </div>
                <div class="mb-3">
                    <label class="text-muted small d-block">Status</label>
                    <span class="badge bg-primary">{{ ucfirst($order->status) }}</span>
                    <span class="badge {{ $order->payment_status === 'paid' ? 'bg-success' : 'bg-danger' }}">{{ ucfirst($order->payment_status) }}</span>
                </div>
                <div class="mb-3">
                    <label class="text-muted small d-block">Payment Method</label>
                    <span class="text-uppercase">{{ $order->payment_method ?? 'N/A' }}</span>
                </div>
                @if($order->notes)
                <div class="mb-0">
                    <label class="text-muted small d-block">Notes</label>
                    <p class="mb-0 small">{{ $order->notes }}</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
