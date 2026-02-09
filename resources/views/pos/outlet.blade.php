@extends('layouts.app')
@section('title', 'POS - ' . $outlet->name)
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">POS: {{ $outlet->name }}</h1>
    <a href="{{ route('pos.index') }}" class="btn btn-outline-secondary">Back to Outlets</a>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif
@if($errors->any())
    <div class="alert alert-danger">{{ $errors->first() }}</div>
@endif

<div class="row">
    <div class="col-lg-4">
        {{-- New Order --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">New Order</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('pos.order.create') }}">
                    @csrf
                    <input type="hidden" name="outlet_id" value="{{ $outlet->id }}">
                    <div class="mb-3">
                        <label class="form-label">Table (optional)</label>
                        <select name="pos_table_id" class="form-select">
                            <option value="">— None —</option>
                            @foreach($tables as $t)
                            <option value="{{ $t->id }}">{{ $t->name }} ({{ $t->status }})</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="btn btn-success w-100">Create Order</button>
                </form>
            </div>
        </div>

        {{-- Tables: Add / Delete --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-light">
                <h5 class="mb-0">Tables</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('pos.outlet.tables.store', $outlet->id) }}" class="d-flex gap-2 mb-3">
                    @csrf
                    <input type="text" name="name" class="form-control" placeholder="Table name" maxlength="50" required>
                    <button type="submit" class="btn btn-outline-primary flex-shrink-0">Add</button>
                </form>
                @if($tables->isEmpty())
                    <p class="text-muted small mb-0">No tables. Add one above.</p>
                @else
                    <ul class="list-group list-group-flush">
                        @foreach($tables as $t)
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span>{{ $t->name }} <span class="badge bg-secondary">{{ $t->status }}</span></span>
                            <form action="{{ route('pos.tables.destroy', $t) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('Delete this table?') }}');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                            </form>
                        </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>

        {{-- Open Orders --}}
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-light">
                <h5 class="mb-0">Open Orders</h5>
            </div>
            <div class="card-body p-0">
                <ul class="list-group list-group-flush">
                    @foreach($openOrders as $ord)
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <a href="{{ route('pos.order', $ord->id) }}">{{ $ord->order_number }}</a>
                        <span class="badge bg-secondary">{{ $ord->status }}</span>
                    </li>
                    @endforeach
                    @if($openOrders->isEmpty())
                    <li class="list-group-item text-muted">No open orders</li>
                    @endif
                </ul>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-light">
                <h5 class="mb-0">Menu</h5>
                <p class="small text-muted mb-0 mt-1">Create an order on the left, then add items on the order page.</p>
            </div>
            <div class="card-body">
                @if($menuItems->isNotEmpty())
                    @foreach($menuItems->groupBy('menu_category_id') as $catId => $items)
                    <div class="mb-4">
                        <h6 class="text-uppercase text-muted border-bottom pb-2 mb-3">{{ $items->first()->menuCategory->name ?? 'Category' }}</h6>
                        <div class="row g-3">
                            @foreach($items as $mi)
                            <div class="col-6 col-md-4 col-lg-3">
                                <div class="card h-100 border shadow-sm overflow-hidden">
                                    <div class="bg-light" style="height: 100px;">
                                        <img src="{{ $mi->image_url }}" alt="{{ $mi->name }}" style="width:100%; height:100%; object-fit: cover;">
                                    </div>
                                    <div class="card-body p-2 text-center">
                                        <div class="fw-semibold text-truncate small" title="{{ $mi->name }}">{{ $mi->name }}</div>
                                        <div class="text-primary fw-bold">{{ money($mi->price) }}</div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endforeach
                @else
                    <p class="text-muted mb-0">No menu items. <a href="{{ route('menu.items.index', $outlet) }}">Add menu items</a>.</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
