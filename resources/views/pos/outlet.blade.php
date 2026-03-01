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
    <div class="col-lg-12">
        {{-- Tables Visual Grid --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Tables Layout</h5>
                <form method="POST" action="{{ route('pos.outlet.tables.store', $outlet->id) }}" class="d-flex gap-2 align-items-center">
                    @csrf
                    <div class="input-group input-group-sm">
                        <input type="text" name="name" class="form-control" placeholder="Table Name" maxlength="50" required>
                        <input type="number" name="capacity" class="form-control text-center" placeholder="Capacity" min="1" style="max-width: 80px;">
                        <input type="number" name="sort_order" class="form-control text-center" placeholder="Sort" min="0" value="0" style="max-width: 60px;">
                        <button type="submit" class="btn btn-primary px-3">Add Table</button>
                    </div>
                </form>
            </div>
            <div class="card-body">
                @if($tables->isEmpty())
                    <div class="text-center py-4 text-muted">
                        <i class="bi bi-grid-3x3 h1 d-block mb-3"></i>
                        No tables added yet.
                    </div>
                @else
                    <div class="row row-cols-2 row-cols-md-4 row-cols-lg-6 g-3">
                        @foreach($tables as $t)
                        @php
                            $openOrder = $openOrders->firstWhere('pos_table_id', $t->id);
                            $isBooked = $openOrder !== null;
                        @endphp
                        <div class="col">
                            <div class="card h-100 border-0 shadow-sm table-card {{ $isBooked ? 'bg-danger text-white border-danger' : 'bg-success-subtle border-success' }}" style="border-width: 2px !important;">
                                <div class="card-body p-3 text-center">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <span class="badge {{ $isBooked ? 'bg-white text-danger' : 'bg-success' }}">
                                            {{ $isBooked ? 'Booked' : 'Available' }}
                                        </span>
                                        <form action="{{ route('pos.tables.destroy', $t) }}" method="POST" onsubmit="return confirm('{{ __('Delete table?') }}');">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-link {{ $isBooked ? 'link-light' : 'link-secondary' }} p-0 border-0"><i class="bi bi-x-circle"></i></button>
                                        </form>
                                    </div>
                                    
                                    <h5 class="card-title fw-bold mb-1">{{ $t->name }}</h5>
                                    
                                    @if($isBooked)
                                        @if($openOrder)
                                            <p class="small text-white-50 mb-1">#{{ $openOrder->order_number }}</p>
                                            <a href="{{ route('pos.order', $openOrder->id) }}" class="btn btn-sm btn-light w-100">View Order</a>
                                        @else
                                            <p class="small text-white-50 mb-1">Booked</p>
                                            <span class="btn btn-sm btn-outline-light w-100 disabled">In Use</span>
                                        @endif
                                    @else
                                        <p class="small text-muted mb-2">Ready for Guest</p>
                                        <form method="POST" action="{{ route('pos.order.create') }}">
                                            @csrf
                                            <input type="hidden" name="outlet_id" value="{{ $outlet->id }}">
                                            <input type="hidden" name="pos_table_id" value="{{ $t->id }}">
                                            <button type="submit" class="btn btn-sm btn-success w-100">Open Order</button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        {{-- General Order (No table) --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Dine-Out / No Table</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('pos.order.create') }}">
                    @csrf
                    <input type="hidden" name="outlet_id" value="{{ $outlet->id }}">
                    <button type="submit" class="btn btn-outline-primary w-100">Create Generic Order</button>
                </form>
            </div>
        </div>

        {{-- Open Orders List --}}
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Active Orders</h5>
                <span class="badge bg-primary rounded-pill">{{ $openOrders->count() }}</span>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush" style="max-height: 400px; overflow-y: auto;">
                    @foreach($openOrders as $ord)
                    <div class="list-group-item">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <a href="{{ route('pos.order', $ord->id) }}" class="fw-bold">#{{ $ord->order_number }}</a>
                            <span class="badge bg-info text-dark">{{ ucfirst($ord->status) }}</span>
                        </div>
                        <div class="small text-muted">
                            @if($ord->posTable)
                                <i class="bi bi-hash"></i> {{ $ord->posTable->name }}
                            @else
                                <i class="bi bi-bag"></i> Takeaway
                            @endif
                            <span class="ms-2"><i class="bi bi-clock"></i> {{ $ord->created_at->format('H:i') }}</span>
                        </div>
                    </div>
                    @endforeach
                    @if($openOrders->isEmpty())
                    <div class="p-3 text-center text-muted">No open orders</div>
                    @endif
                </div>
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
