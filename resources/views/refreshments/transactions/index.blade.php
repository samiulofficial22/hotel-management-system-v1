@extends('layouts.app')
@section('title', 'Consumption History')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Consumption History</h1>
    <a href="{{ route('refreshments.transactions.create') }}" class="btn btn-primary">Record Consumption</a>
</div>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form action="{{ route('refreshments.transactions.index') }}" method="GET" class="row g-3">
            <div class="col-md-3">
                <select name="room_id" class="form-select">
                    <option value="">All Rooms</option>
                    @foreach($rooms as $room)
                        <option value="{{ $room->id }}" {{ request('room_id') == $room->id ? 'selected' : '' }}>Room {{ $room->number }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <select name="item_id" class="form-select">
                    <option value="">All Items</option>
                    @foreach($items as $item)
                        <option value="{{ $item->id }}" {{ request('item_id') == $item->id ? 'selected' : '' }}>{{ $item->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <input type="date" name="date" class="form-control" value="{{ request('date') }}">
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-outline-secondary w-100">Filter</button>
            </div>
        </form>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>Date & Time</th>
                        <th>Room</th>
                        <th>Guest</th>
                        <th>Item</th>
                        <th>Qty</th>
                        <th>Unit Price</th>
                        <th>Total</th>
                        <th>Recorded By</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($transactions as $tx)
                    <tr>
                        <td>{{ $tx->created_at->format('d M, Y h:i A') }}</td>
                        <td>Room {{ $tx->room->number }}</td>
                        <td>{{ $tx->booking->guest->name ?? 'N/A' }}</td>
                        <td>{{ $tx->item->name }}</td>
                        <td>{{ $tx->quantity }}</td>
                        <td>{{ money($tx->unit_price) }}</td>
                        <td>{{ money($tx->total_price) }}</td>
                        <td>{{ $tx->recordedBy->name }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        {{ $transactions->links() }}
    </div>
</div>
@endsection
