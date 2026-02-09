@extends('layouts.app')
@section('title', 'Order ' . $order->order_number)
@section('content')
<div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-4">
    <div>
        <h1 class="h3 mb-1">Order {{ $order->order_number }}</h1>
        <p class="text-muted small mb-0">
            {{ $order->outlet->name }}
            @if($order->posTable) · Table: {{ $order->posTable->name }} @endif
            · {{ str_replace('_', ' ', ucfirst($order->pos_type ?? 'restaurant')) }}
            @if($order->guest) · {{ $order->guest->name }} @endif
            @if($order->booking) · Room: {{ $order->booking->room->number ?? '-' }} @endif
            <br>
            <strong>Started:</strong> {{ $order->created_at->format('M d, h:i A') }}
            @if($order->completed_at) · <strong>Done:</strong> {{ $order->completed_at->format('h:i A') }} @endif
        </p>
    </div>
    <div class="d-flex align-items-center gap-2">
        @if($order->payment_status === 'paid')
            <span class="badge bg-success fs-6">Paid</span>
        @elseif($order->payment_status === 'posted_to_room')
            <span class="badge bg-info fs-6">Posted to room</span>
        @elseif($order->payment_status === 'voided')
            <span class="badge bg-secondary fs-6">Voided</span>
        @else
            <span class="badge bg-warning text-dark fs-6">Pending payment</span>
        @endif
        <a href="{{ route('pos.outlet', $order->outlet_id) }}" class="btn btn-outline-secondary btn-sm">Back to {{ $order->outlet->name }}</a>
    </div>
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
    {{-- Current order items --}}
    <div class="col-lg-5">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Current items</h5>
                <span class="fw-bold text-primary">Total: {{ money($order->total) }}</span>
            </div>
            <div class="card-body p-0">
                @if($order->items->isEmpty())
                    <p class="text-muted p-3 mb-0">No items yet. Add items from the menu on the right.</p>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover table-sm mb-0">
                            <thead><tr><th>Item</th><th class="text-center">Qty</th><th class="text-end">Price</th><th class="text-end">Total</th><th></th></tr></thead>
                            <tbody>
                            @foreach($order->items as $line)
                                @php($mi = $line->menuItem)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <img src="{{ $mi?->image_url ?? asset('images/placeholder-food.svg') }}" alt="" class="rounded" width="36" height="36" style="object-fit: cover;">
                                            <span>{{ $mi->name ?? '-' }}</span>
                                        </div>
                                    </td>
                                    <td class="text-center">{{ $line->quantity }}</td>
<td class="text-end">{{ money($line->unit_price) }}</td>
                                        <td class="text-end">{{ money($line->total) }}</td>
                                    <td class="text-end">
                                        @if(!$order->isPaid() && !$order->isPostedToRoom() && !$order->isVoided())
                                        <form action="{{ route('pos.order.remove-item', [$order->id, $line->id]) }}" method="POST" class="d-inline" onsubmit="return confirm('Remove this item?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger">Remove</button>
                                        </form>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
            <div class="card-footer bg-light">
                @if($order->items->isNotEmpty() && !$order->isPaid() && !$order->isPostedToRoom() && !$order->isVoided())
                <form action="{{ route('pos.order.send-kitchen', $order->id) }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-warning btn-sm">Send to Kitchen</button>
                </form>
                <form action="{{ route('pos.order.complete', $order->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Complete order? You can then Pay or Post to room.');">
                    @csrf
                    <button type="submit" class="btn btn-success btn-sm">Complete Order</button>
                </form>
                @endif
                @if($order->status === 'completed' && $order->payment_status === 'pending' && $order->total > 0)
                <div class="mb-2">
                    <strong class="small">Payment</strong>
                    <form action="{{ route('pos.order.pay', $order->id) }}" method="POST" class="d-inline-flex flex-wrap gap-2 align-items-end mt-1">
                        @csrf
                        <input type="number" name="payments[0][amount]" step="0.01" value="{{ $order->total }}" class="form-control form-control-sm" style="width:90px" placeholder="Amount">
                        <select name="payments[0][method]" class="form-select form-select-sm" style="width:120px">
                            <option value="cash">Cash</option>
                            <option value="card">Card</option>
                            <option value="mobile_banking">Mobile Banking</option>
                            <option value="bank_transfer">Bank Transfer</option>
                        </select>
                        <button type="submit" class="btn btn-success btn-sm">Pay now</button>
                    </form>
                </div>
                <form action="{{ route('pos.order.post-to-room', $order->id) }}" method="POST" class="mb-0">
                    @csrf
                    @if($order->booking_id)
                        <button type="submit" class="btn btn-info btn-sm">Post to room ({{ $order->booking->room->number ?? $order->booking->booking_number }})</button>
                    @else
                        <div class="d-flex flex-wrap gap-2 align-items-end">
                            <select name="booking_id" class="form-select form-select-sm" style="width:200px" required>
                                <option value="">— Select room/booking —</option>
                                @foreach($activeBookings as $b)
                                <option value="{{ $b->id }}">{{ $b->room->number ?? '-' }} - {{ $b->guest->name ?? $b->booking_number }}</option>
                                @endforeach
                            </select>
                            <button type="submit" class="btn btn-info btn-sm">Post to room</button>
                        </div>
                        @if($activeBookings->isEmpty())
                        <p class="small text-muted mt-1 mb-0">No checked-in bookings.</p>
                        @endif
                    @endif
                </form>
                @endif
                @if($order->canVoid() && !$order->isPaid() && auth()->user()?->can('pos.void'))
                <form action="{{ route('pos.order.void', $order->id) }}" method="POST" class="d-inline mt-2" onsubmit="return confirm('Void this order? Manager only.');">
                    @csrf
                    <button type="submit" class="btn btn-outline-danger btn-sm">Void order</button>
                </form>
                @endif
            </div>
        </div>
    </div>

    {{-- Menu: add items --}}
    <div class="col-lg-7">
        @if(!$order->isPaid() && !$order->isPostedToRoom() && !$order->isVoided())
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-body py-2">
                <form method="POST" action="{{ route('pos.order.add-item', $order->id) }}" class="row g-2 align-items-end">
                    @csrf
                    <div class="col-md-5">
                        <label class="form-label small mb-0">Quick add</label>
                        <select name="menu_item_id" class="form-select form-select-sm" required>
                            <option value="">— Select item —</option>
                            @foreach($menuItems->groupBy('menu_category_id') as $catId => $items)
                            <optgroup label="{{ $items->first()->menuCategory->name ?? 'Category' }}">
                            @foreach($items as $mi)
                            <option value="{{ $mi->id }}">{{ $mi->name }} — {{ money($mi->price) }}</option>
                            @endforeach
                            </optgroup>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small mb-0">Qty</label>
                        <input type="number" name="quantity" value="1" min="1" max="99" class="form-control form-control-sm">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small mb-0">Notes (optional)</label>
                        <input type="text" name="notes" class="form-control form-control-sm" placeholder="e.g. no ice">
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary btn-sm w-100">Add</button>
                    </div>
                </form>
            </div>
        </div>
        @endif

        <h5 class="mb-3">Menu — tap to add</h5>
        @if($menuItems->isEmpty())
            <p class="text-muted">No menu items.</p>
        @else
            @foreach($menuItems->groupBy('menu_category_id') as $catId => $items)
            <div class="mb-4">
                <h6 class="text-uppercase text-muted border-bottom pb-2 mb-3">{{ $items->first()->menuCategory->name ?? 'Category' }}</h6>
                <div class="row g-3">
                    @foreach($items as $mi)
                    <div class="col-6 col-md-4">
                        <div class="card h-100 border shadow-sm overflow-hidden">
                            <div class="bg-light" style="height: 90px;">
                                <img src="{{ $mi->image_url }}" alt="{{ $mi->name }}" style="width:100%; height:100%; object-fit: cover;">
                            </div>
                            <div class="card-body p-2">
                                <div class="fw-semibold text-truncate small" title="{{ $mi->name }}">{{ $mi->name }}</div>
                                <div class="text-primary fw-bold small">{{ money($mi->price) }}</div>
                                @if(!$order->isPaid() && !$order->isPostedToRoom() && !$order->isVoided())
                                <form method="POST" action="{{ route('pos.order.add-item', $order->id) }}" class="mt-2">
                                    @csrf
                                    <input type="hidden" name="menu_item_id" value="{{ $mi->id }}">
                                    <div class="d-flex gap-1 align-items-center">
                                        <select name="quantity" class="form-select form-select-sm" style="width:60px">
                                            @for($q = 1; $q <= 9; $q++) <option value="{{ $q }}">{{ $q }}</option> @endfor
                                        </select>
                                        <button type="submit" class="btn btn-primary btn-sm flex-grow-1">Add</button>
                                    </div>
                                </form>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endforeach
        @endif
        @if($order->isPaid() || $order->isPostedToRoom() || $order->isVoided())
        <p class="text-muted">Order is closed. No more items can be added.</p>
        @endif
    </div>
</div>
@endsection
