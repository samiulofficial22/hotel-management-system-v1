@extends('layouts.app')
@section('title', 'Kitchen Display')
@section('content')
<h1 class="h3 mb-4">Kitchen Display</h1>
@foreach($orders as $order)
<div class="card mb-3">
    <div class="card-header d-flex justify-content-between">
        <strong>{{ $order->order_number }}</strong>
        <span class="badge bg-secondary">{{ $order->outlet->name }} @if($order->posTable) | {{ $order->posTable->name }} @endif</span>
    </div>
    <div class="card-body">
        <ul class="list-group list-group-flush">
        @foreach($order->items as $item)
        @if(!in_array($item->status, ['ready']))
        <li class="list-group-item d-flex justify-content-between align-items-center">
            <span>{{ $item->menuItem->name ?? '-' }} x {{ $item->quantity }} @if($item->notes)<small class="text-muted">({{ $item->notes }})</small>@endif</span>
            <form action="{{ route('kitchen.item.ready', $item) }}" method="POST" class="d-inline">
            @csrf
            <button type="submit" class="btn btn-sm btn-success">Mark Ready</button>
            </form>
        </li>
        @endif
        @endforeach
        </ul>
        @if($order->items->every(fn($i) => $i->status === 'ready'))
        <p class="mb-0 mt-2 small text-success">All items ready</p>
        @endif
    </div>
</div>
@endforeach
@if($orders->isEmpty())
<p class="text-muted">No pending orders for kitchen.</p>
@endif
@endsection
