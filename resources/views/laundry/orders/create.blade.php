@extends('layouts.app')
@section('title', 'Create Laundry Order')
@section('content')
<div class="mb-4">
    <a href="{{ route('laundry.orders.index') }}" class="btn btn-link text-decoration-none p-0">
        <i class="bi bi-arrow-left"></i> Back to Orders
    </a>
    <h1 class="h3 mb-0 mt-2">New Laundry Order</h1>
</div>

<form action="{{ route('laundry.orders.store') }}" method="POST" id="orderForm">
    @csrf
    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="card-title mb-0">Order Items</h5>
                </div>
                <div class="card-body">
                    <div id="itemsContainer">
                        <div class="item-row row g-2 mb-3 align-items-end border-bottom pb-3">
                            <div class="col-md-5">
                                <label class="form-label small">Select Item</label>
                                <select name="items[0][laundry_item_id]" class="form-select laundry-item-select" required>
                                    <option value="">-- Choose Item --</option>
                                    @foreach($items as $item)
                                        <option value="{{ $item->id }}" 
                                            data-wash="{{ $item->wash_price }}" 
                                            data-iron="{{ $item->iron_price }}" 
                                            data-dryclean="{{ $item->dry_clean_price }}">
                                            {{ $item->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small">Service Type</label>
                                <select name="items[0][service_type]" class="form-select service-type-select" required>
                                    <option value="wash">Wash</option>
                                    <option value="iron">Iron</option>
                                    <option value="dry_clean">Dry Clean</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label small">Qty</label>
                                <input type="number" name="items[0][quantity]" class="form-control quantity-input" value="1" min="1" required>
                            </div>
                            <div class="col-md-2 text-end">
                                <button type="button" class="btn btn-outline-danger btn-sm remove-item" disabled>
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <button type="button" class="btn btn-outline-primary btn-sm mt-2" id="addItem">
                        <i class="bi bi-plus-lg"></i> Add Another Item
                    </button>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="card-title mb-0">Order Details</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Room</label>
                        <select name="room_id" class="form-select" required>
                            <option value="">-- Select Room --</option>
                            @foreach($rooms as $room)
                                <option value="{{ $room->id }}">{{ $room->number }} ({{ $room->latestBooking->guest->full_name ?? 'N/A' }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Payment Method</label>
                        <select name="payment_method" class="form-select">
                            <option value="cash">Cash</option>
                            <option value="bank">Bank/Card</option>
                            <option value="post_to_room">Post to Room Bill</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Payment Status</label>
                        <select name="payment_status" class="form-select">
                            <option value="unpaid">Unpaid</option>
                            <option value="paid">Paid</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Initial Status</label>
                        <select name="status" class="form-select">
                            <option value="pending">Pending</option>
                            <option value="processing">Processing</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Notes</label>
                        <textarea name="notes" class="form-control" rows="2"></textarea>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Total Amount:</span>
                        <span class="fw-bold h5 mb-0">৳<span id="totalDisplay">0.00</span></span>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 py-2 mt-2">Place Order</button>
                </div>
            </div>
        </div>
    </div>
</form>

@push('scripts')
<script>
    let itemIndex = 1;
    const itemsContainer = document.getElementById('itemsContainer');
    const totalDisplay = document.getElementById('totalDisplay');

    function calculateTotal() {
        let total = 0;
        document.querySelectorAll('.item-row').forEach(row => {
            const select = row.querySelector('.laundry-item-select');
            const type = row.querySelector('.service-type-select').value;
            const qty = parseInt(row.querySelector('.quantity-input').value) || 0;
            
            if (select.value) {
                const option = select.options[select.selectedIndex];
                let price = 0;
                if (type === 'wash') price = parseFloat(option.dataset.wash);
                else if (type === 'iron') price = parseFloat(option.dataset.iron);
                else if (type === 'dry_clean') price = parseFloat(option.dataset.dryclean);
                
                total += price * qty;
            }
        });
        totalDisplay.innerText = total.toFixed(2);
    }

    document.getElementById('addItem').addEventListener('click', () => {
        const firstRow = document.querySelector('.item-row');
        const newRow = firstRow.cloneNode(true);
        
        newRow.querySelectorAll('select, input').forEach(el => {
            el.name = el.name.replace('[0]', `[${itemIndex}]`);
            if (el.tagName === 'INPUT') el.value = 1; else el.value = '';
        });
        
        newRow.querySelector('.remove-item').disabled = false;
        itemsContainer.appendChild(newRow);
        itemIndex++;
    });

    itemsContainer.addEventListener('click', (e) => {
        if (e.target.closest('.remove-item')) {
            e.target.closest('.item-row').remove();
            calculateTotal();
        }
    });

    itemsContainer.addEventListener('change', (e) => {
        if (e.target.classList.contains('laundry-item-select') || 
            e.target.classList.contains('service-type-select') || 
            e.target.classList.contains('quantity-input')) {
            calculateTotal();
        }
    });

    calculateTotal();
</script>
@endpush
@endsection
