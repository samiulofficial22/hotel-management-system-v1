@extends('layouts.app')
@section('title', 'POS - ' . $outlet->name)
@section('content')
<h1 class="h3 mb-4">POS: {{ $outlet->name }}</h1>
<div class="row">
<div class="col-md-4">
    <h5>New Order</h5>
    <form method="POST" action="{{ route('pos.order.create') }}" class="mb-3">
    @csrf
    <input type="hidden" name="outlet_id" value="{{ $outlet->id }}">
    <div class="mb-2">
        <label class="form-label">Table (optional)</label>
        <select name="pos_table_id" class="form-select">
            <option value="">— None —</option>
            @foreach($tables as $t)
            <option value="{{ $t->id }}">{{ $t->name }} ({{ $t->status }})</option>
            @endforeach
        </select>
    </div>
    <button type="submit" class="btn btn-success">Create Order</button>
    </form>
    <h5 class="mt-4">Open Orders</h5>
    <ul class="list-group">
    @foreach($openOrders as $ord)
    <li class="list-group-item d-flex justify-content-between">
        <a href="{{ route('pos.order', $ord->id) }}">{{ $ord->order_number }}</a>
        <span class="badge bg-secondary">{{ $ord->status }}</span>
    </li>
    @endforeach
    @if($openOrders->isEmpty())
    <li class="list-group-item text-muted">No open orders</li>
    @endif
    </ul>
</div>
<div class="col-md-8">
    <h5>Menu (add items from order screen)</h5>
    <p class="small text-muted">Create an order above, then add items on the order page.</p>
    @if($menuItems->isNotEmpty())
    <div class="row g-2">
    @foreach($menuItems->groupBy('menu_category_id') as $catId => $items)
    <div class="col-12">
        <strong>{{ $items->first()->menuCategory->name ?? 'Category' }}</strong>
        <div class="d-flex flex-wrap gap-1 mt-1">
        @foreach($items as $mi)
        <span class="badge bg-light text-dark">{{ $mi->name }} — {{ number_format($mi->price, 2) }}</span>
        @endforeach
        </div>
    </div>
    @endforeach
    </div>
    @else
    <p class="text-muted">No menu items. <a href="{{ route('menu.items.index', $outlet) }}">Add menu items</a>.</p>
    @endif
</div>
</div>
<a href="{{ route('pos.index') }}" class="btn btn-outline-secondary mt-3">Back to POS</a>
@endsection
