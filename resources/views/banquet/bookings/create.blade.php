@extends('layouts.app')
@section('title', 'New Banquet Booking')
@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <h1 class="h5 mb-0"><i class="fas fa-calendar-plus me-2 text-primary"></i>New Banquet Booking</h1>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('banquet.bookings.store') }}" id="bookingForm">
                    @csrf
                    
                    <!-- Event Details Section -->
                    <h5 class="card-title mb-3 text-muted small text-uppercase fw-bold border-bottom pb-2">Event Information</h5>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Select Venue <span class="text-danger">*</span></label>
                            <select name="banquet_venue_id" class="form-select" id="venueSelect" required>
                                <option value="" disabled selected>Choose a venue...</option>
                                @foreach($venues as $v)
                                <option value="{{ $v->id }}" data-hourly="{{ $v->hourly_rate }}" data-fixed="{{ $v->fixed_rate }}">{{ $v->name }} (Cap: {{ $v->capacity }})</option>
                                @endforeach
                            </select>
                            @error('banquet_venue_id')<div class="text-danger small">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Event Name <span class="text-danger">*</span></label>
                            <input type="text" name="event_name" class="form-control" value="{{ old('event_name') }}" placeholder="e.g. Wedding Reception" required>
                            @error('event_name')<div class="text-danger small">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">Event Date <span class="text-danger">*</span></label>
                            <input type="date" name="event_date" class="form-control" value="{{ old('event_date', date('Y-m-d')) }}" required>
                            @error('event_date')<div class="text-danger small">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">Start Time <span class="text-danger">*</span></label>
                            <input type="time" name="start_time" class="form-control" id="startTime" value="{{ old('start_time') }}" required>
                            @error('start_time')<div class="text-danger small">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">End Time <span class="text-danger">*</span></label>
                            <input type="time" name="end_time" class="form-control" id="endTime" value="{{ old('end_time') }}" required>
                            @error('end_time')<div class="text-danger small">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Guest Count <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-white"><i class="fas fa-users text-muted"></i></span>
                                <input type="number" name="guest_count" class="form-control" min="1" value="{{ old('guest_count') }}" placeholder="0" required>
                            </div>
                            @error('guest_count')<div class="text-danger small">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Package Name</label>
                            <input type="text" name="package_name" class="form-control" value="{{ old('package_name') }}" placeholder="e.g. Premium Gold Menu">
                            @error('package_name')<div class="text-danger small">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <!-- Contact Section -->
                    <h5 class="card-title mb-3 mt-4 text-muted small text-uppercase fw-bold border-bottom pb-2">Customer Information</h5>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Contact Person <span class="text-danger">*</span></label>
                            <input type="text" name="contact_name" class="form-control" value="{{ old('contact_name') }}" placeholder="Account Holder Name" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Contact Phone <span class="text-danger">*</span></label>
                            <input type="text" name="contact_phone" class="form-control" value="{{ old('contact_phone') }}" placeholder="+8801..." required>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="form-label fw-bold">Contact Email</label>
                            <input type="email" name="contact_email" class="form-control" value="{{ old('contact_email') }}" placeholder="customer@example.com">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Notes</label>
                        <textarea name="notes" class="form-control" rows="3" placeholder="Any special requirements or arrangements...">{{ old('notes') }}</textarea>
                    </div>

                    <h5 class="card-title mb-3 mt-4 text-muted small text-uppercase fw-bold border-bottom pb-2">Payment Details</h5>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Payment Method</label>
                            <select name="payment_method" class="form-select">
                                <option value="CASH" {{ old('payment_method') == 'CASH' ? 'selected' : '' }}>Cash</option>
                                <option value="BANK" {{ old('payment_method') == 'BANK' ? 'selected' : '' }}>Bank Transfer / Card</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Log to Accounts</label>
                            <div class="form-text text-muted mt-2">
                                <i class="fas fa-info-circle me-1"></i> Revenue will be automatically logged to accounts when status is set to <strong>Confirmed</strong> or <strong>Completed</strong>.
                            </div>
                        </div>
                    </div>

                    <div class="card bg-light border-0 mt-4 mb-4">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="text-muted small">Estimated Total Amount</span>
                                    <h4 class="mb-0 text-primary">$<span id="estimatedAmount">0.00</span></h4>
                                </div>
                                <div class="w-50">
                                    <label class="form-label small mb-1 fw-bold">Total Amount <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        <input type="number" name="total_amount" id="totalAmount" class="form-control fw-bold h5 mb-0 py-2" step="0.01" min="0" value="{{ old('total_amount', 0) }}" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('banquet.bookings.index') }}" class="btn btn-light px-4">Cancel</a>
                        <button type="submit" class="btn btn-primary px-5 shadow-sm">
                            <i class="fas fa-check-circle me-1"></i> Confirm Booking
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white">
                <h5 class="mb-0 small fw-bold text-uppercase">Booking Policy</h5>
            </div>
            <div class="card-body small">
                <ul class="ps-3 mb-3">
                    <li>Overlap check is performed automatically.</li>
                    <li>Ensure guest count does not exceed venue capacity.</li>
                    <li>Status will be marked as "Pending" by default.</li>
                </ul>
                <div class="alert alert-info border-0 py-2">
                    <i class="fas fa-info-circle me-1"></i> Prices are calculated based on venue rates and duration.
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const venueSelect = document.getElementById('venueSelect');
    const startTime = document.getElementById('startTime');
    const endTime = document.getElementById('endTime');
    const estimatedAmount = document.getElementById('estimatedAmount');
    const totalAmount = document.getElementById('totalAmount');

    function calculatePrice() {
        const option = venueSelect.options[venueSelect.selectedIndex];
        if (!option || !option.value) return;

        const hourly = parseFloat(option.dataset.hourly) || 0;
        const fixed = parseFloat(option.dataset.fixed) || 0;
        
        let price = fixed;

        if (startTime.value && endTime.value && hourly > 0) {
            const start = new Date(`2000-01-01 ${startTime.value}`);
            const end = new Date(`2000-01-01 ${endTime.value}`);
            const diffInMs = end - start;
            if (diffInMs > 0) {
                const diffInHours = diffInMs / (1000 * 60 * 60);
                price += diffInHours * hourly;
            }
        }

        estimatedAmount.innerText = price.toFixed(2);
        if (totalAmount.value == 0 || totalAmount.value == '') {
            totalAmount.value = price.toFixed(2);
        }
    }

    venueSelect.addEventListener('change', calculatePrice);
    startTime.addEventListener('change', calculatePrice);
    endTime.addEventListener('change', calculatePrice);
});
</script>
@endsection

