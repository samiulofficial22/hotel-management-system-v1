@extends('layouts.app')
@section('title', 'Order ' . $order->order_number)
@section('content')
<h1 class="h3 mb-4">Order {{ $order->order_number }}</h1>
<p class="text-muted">Outlet: {{ $order->outlet->name }} @if($order->posTable) | Table: {{ $order->posTable->name }} @endif</p>
<div class="row">
<div class="col-md-6">
    <h5>Current items</h5>
    <table class="table table-sm">
    <thead><tr><th>Item</th><th>Qty</th><th>Price</th><th>Total</th><th></th></tr></thead>
    <tbody>
    @foreach($order->items as $line)
    <tr>
        <td>{{ $line->menuItem->name ?? '-' }}</td>
        <td>{{ $line->quantity }}</td>
        <td>{{ number_format($line->unit_price, 2) }}</td>
        <td>{{ number_format($line->total, 2) }}</td>
        <td>
            <form action="{{ route('pos.order.remove-item', [$order->id, $line->id]) }}" method="POST" class="d-inline" onsubmit="return confirm('Remove this item?');">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-sm btn-outline-danger">Remove</button>
            </form>
        </td>
    </tr>
    @endforeach
    </tbody>
    </table>
    @if($order->items->isEmpty())
    <p class="text-muted">No items yet. Add items below.</p>
    @endif
    <p class="fw-bold">Total: {{ number_format($order->total, 2) }}</p>
    @if($order->items->isNotEmpty())
    <form action="{{ route('pos.order.send-kitchen', $order->id) }}" method="POST" class="d-inline">
    @csrf
    <button type="submit" class="btn btn-warning">Send to Kitchen</button>
    </form>
    <form action="{{ route('pos.order.complete', $order->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Complete and close this order?');">
    @csrf
    <button type="submit" class="btn btn-success">Complete Order</button>
    </form>
    @endif
</div>
<div class="col-md-6">
    <h5>Add item</h5>
    <form method="POST" action="{{ route('pos.order.add-item', $order->id) }}" class="row g-2">
    @csrf
    <div class="col-12">
        <label class="form-label">Menu item</label>
        <select name="menu_item_id" class="form-select" required>
            <option value="">— Select —</option>
            @foreach($menuItems->groupBy('menu_category_id') as $catId => $items)
            <optgroup label="{{ $items->first()->menuCategory->name ?? 'Category' }}">
            @foreach($items as $mi)
            <option value="{{ $mi->id }}">{{ $mi->name }} — {{ number_format($mi->price, 2) }}</option>
            @endforeach
            </optgroup>
            @endforeach
        </select>
    </div>
    <div class="col-6">
        <label class="form-label">Qty</label>
        <input type="number" name="quantity" value="1" min="1" class="form-control">
    </div>
    <div class="col-12">
        <label class="form-label">Notes (optional)</label>
        <input type="text" name="notes" class="form-control">
    </div>
    <div class="col-12">
        <button type="submit" class="btn btn-primary">Add Item</button>
    </div>
    </form>
</div>
</div>
<a href="{{ route('pos.outlet', $order->outlet_id) }}" class="btn btn-outline-secondary mt-3">Back to {{ $order->outlet->name }}</a>
@endsection
