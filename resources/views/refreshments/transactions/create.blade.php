@extends('layouts.app')
@section('title', 'Record Consumption')
@section('content')
<div class="mb-4">
    <a href="{{ route('refreshments.transactions.index') }}" class="btn btn-link text-decoration-none p-0">
        <i class="bi bi-arrow-left"></i> Back to History
    </a>
    <h1 class="h3 mb-0 mt-2">Record Room Consumption</h1>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <form action="{{ route('refreshments.transactions.store') }}" method="POST">
            @csrf
            <div class="row g-4">
                <div class="col-md-6">
                    <label class="form-label">Select Room / Booking</label>
                    <select name="booking_id" class="form-select" required>
                        <option value="">-- Choose Booking --</option>
                        @foreach($bookings as $booking)
                            <option value="{{ $booking->id }}">
                                Room {{ $booking->room->number }} - {{ $booking->guest->name }} (#{{ $booking->id }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Select Item</label>
                    <select name="item_id" class="form-select" id="itemSelect" required>
                        <option value="">-- Choose Item --</option>
                        @foreach($items as $item)
                            <option value="{{ $item->id }}" data-price="{{ $item->price }}" data-stock="{{ $item->stock_quantity }}">
                                {{ $item->name }} (Price: {{ money($item->price) }}, Stock: {{ $item->stock_quantity }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Quantity</label>
                    <input type="number" name="quantity" id="qtyInput" class="form-control" value="1" min="1" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Unit Price (৳)</label>
                    <input type="text" id="priceDisplay" class="form-control bg-light" readonly>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Total Charge (৳)</label>
                    <input type="text" id="totalDisplay" class="form-control bg-light fw-bold text-primary" readonly>
                </div>
                <div class="col-12 text-end">
                    <button type="submit" class="btn btn-primary px-5 py-2">Record & Charge to Room</button>
                </div>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    const itemSelect = document.getElementById('itemSelect');
    const qtyInput = document.getElementById('qtyInput');
    const priceDisplay = document.getElementById('priceDisplay');
    const totalDisplay = document.getElementById('totalDisplay');

    function updateCalculations() {
        const option = itemSelect.options[itemSelect.selectedIndex];
        if (option && option.value) {
            const price = parseFloat(option.dataset.price);
            const qty = parseInt(qtyInput.value) || 0;
            const stock = parseInt(option.dataset.stock);

            if (qty > stock) {
                // Warning only, for now
            }

            priceDisplay.value = price.toFixed(2);
            totalDisplay.value = (price * qty).toFixed(2);
        } else {
            priceDisplay.value = '';
            totalDisplay.value = '';
        }
    }

    itemSelect.addEventListener('change', updateCalculations);
    qtyInput.addEventListener('input', updateCalculations);
</script>
@endpush
@endsection
