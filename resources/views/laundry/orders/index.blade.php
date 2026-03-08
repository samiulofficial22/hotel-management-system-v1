@extends('layouts.app')
@section('title', 'Laundry Orders')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Laundry Orders</h1>
    <a href="{{ route('laundry.orders.create') }}" class="btn btn-primary">New Order</a>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>Order #</th>
                        <th>Room</th>
                        <th>Guest</th>
                        <th>Order Date/Time</th>
                        <th>Total Amount</th>
                        <th>Status</th>
                        <th>Payment</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($orders as $order)
                    <tr>
                        <td class="fw-bold">#{{ $order->id }}</td>
                        <td>{{ $order->room->number }}</td>
                        <td>{{ $order->guest->full_name ?? 'N/A' }}</td>
                        <td>
                            <div class="small fw-bold text-dark">{{ $order->order_date ? date('d M, Y', strtotime($order->order_date)) : 'N/A' }}</div>
                            <div class="small text-muted">{{ $order->order_time ? date('h:i A', strtotime($order->order_time)) : '' }}</div>
                        </td>
                        <td>{{ money($order->total_amount) }}</td>
                        <td>
                            @php
                                $statusClass = match($order->status) {
                                    'pending' => 'bg-warning',
                                    'processing' => 'bg-info',
                                    'ready' => 'bg-primary',
                                    'delivered' => 'bg-success',
                                    'cancelled' => 'bg-danger',
                                    default => 'bg-secondary'
                                };
                            @endphp
                            <span class="badge {{ $statusClass }}">{{ ucfirst($order->status) }}</span>
                        </td>
                        <td>
                            <span class="badge {{ $order->payment_status === 'paid' ? 'bg-success' : 'bg-danger' }}">
                                {{ ucfirst($order->payment_status) }}
                            </span>
                        </td>
                        <td class="text-end">
                            <a href="{{ route('laundry.orders.show', $order) }}" class="btn btn-sm btn-outline-info">View</a>
                            <form action="{{ route('laundry.orders.destroy', $order) }}" method="POST" class="d-inline">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete?')">Delete</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        {{ $orders->links() }}
    </div>
</div>
@endsection
