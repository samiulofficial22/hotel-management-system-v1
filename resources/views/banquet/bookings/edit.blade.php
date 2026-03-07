@extends('layouts.app')
@section('title', 'Edit Banquet Booking')
@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h1 class="h5 mb-0"><i class="fas fa-edit me-2 text-primary"></i>Edit Banquet Booking</h1>
                <span class="badge {{ \App\Models\BanquetBooking::statusBadgeConfig($booking->status)['class'] }}">
                    {{ \App\Models\BanquetBooking::statusBadgeConfig($booking->status)['label'] }}
                </span>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('banquet.bookings.update', $booking) }}" id="bookingForm">
                    @csrf
                    @method('PUT')
                    
                    <!-- Event Details Section -->
                    <h5 class="card-title mb-3 text-muted small text-uppercase fw-bold border-bottom pb-2">Event Information</h5>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Venue <span class="text-danger">*</span></label>
                            <select name="banquet_venue_id" class="form-select" id="venueSelect" required>
                                @foreach($venues as $v)
                                <option value="{{ $v->id }}" data-hourly="{{ $v->hourly_rate }}" data-fixed="{{ $v->fixed_rate }}" {{ old('banquet_venue_id', $booking->banquet_venue_id) == $v->id ? 'selected' : '' }}>
                                    {{ $v->name }} (Cap: {{ $v->capacity }})
                                </option>
                                @endforeach
                            </select>
                            @error('banquet_venue_id')<div class="text-danger small">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Event Name <span class="text-danger">*</span></label>
                            <input type="text" name="event_name" class="form-control" value="{{ old('event_name', $booking->event_name) }}" required>
                            @error('event_name')<div class="text-danger small">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">Event Date <span class="text-danger">*</span></label>
                            <input type="date" name="event_date" class="form-control" value="{{ old('event_date', $booking->event_date->format('Y-m-d')) }}" required>
                            @error('event_date')<div class="text-danger small">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">Start Time <span class="text-danger">*</span></label>
                            <input type="time" name="start_time" class="form-control" id="startTime" value="{{ old('start_time', \Carbon\Carbon::parse($booking->start_time)->format('H:i')) }}" required>
                            @error('start_time')<div class="text-danger small">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">End Time <span class="text-danger">*</span></label>
                            <input type="time" name="end_time" class="form-control" id="endTime" value="{{ old('end_time', \Carbon\Carbon::parse($booking->end_time)->format('H:i')) }}" required>
                            @error('end_time')<div class="text-danger small">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Guest Count <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-white"><i class="fas fa-users text-muted"></i></span>
                                <input type="number" name="guest_count" class="form-control" min="1" value="{{ old('guest_count', $booking->guest_count) }}" required>
                            </div>
                            @error('guest_count')<div class="text-danger small">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Package Name</label>
                            <input type="text" name="package_name" class="form-control" value="{{ old('package_name', $booking->package_name) }}">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Status <span class="text-danger">*</span></label>
                        <select name="status" class="form-select fw-bold">
                            @foreach([\App\Models\BanquetBooking::STATUS_PENDING, \App\Models\BanquetBooking::STATUS_CONFIRMED, \App\Models\BanquetBooking::STATUS_CANCELLED, \App\Models\BanquetBooking::STATUS_COMPLETED] as $s)
                            @php $cfg = \App\Models\BanquetBooking::statusBadgeConfig($s); @endphp
                            <option value="{{ $s }}" {{ old('status', $booking->status) == $s ? 'selected' : '' }}>{{ $cfg['label'] }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Contact Section -->
                    <h5 class="card-title mb-3 mt-4 text-muted small text-uppercase fw-bold border-bottom pb-2">Customer Information</h5>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Contact Person <span class="text-danger">*</span></label>
                            <input type="text" name="contact_name" class="form-control" value="{{ old('contact_name', $booking->contact_name) }}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Contact Phone <span class="text-danger">*</span></label>
                            <input type="text" name="contact_phone" class="form-control" value="{{ old('contact_phone', $booking->contact_phone) }}" required>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="form-label fw-bold">Contact Email</label>
                            <input type="email" name="contact_email" class="form-control" value="{{ old('contact_email', $booking->contact_email) }}">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Notes</label>
                        <textarea name="notes" class="form-control" rows="3">{{ old('notes', $booking->notes) }}</textarea>
                    </div>

                    <h5 class="card-title mb-3 mt-4 text-muted small text-uppercase fw-bold border-bottom pb-2">Payment & Accounting</h5>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Payment Method</label>
                            <select name="payment_method" class="form-select">
                                <option value="CASH" {{ old('payment_method', $booking->payment_method) == 'CASH' ? 'selected' : '' }}>Cash</option>
                                <option value="BANK" {{ old('payment_method', $booking->payment_method) == 'BANK' ? 'selected' : '' }}>Bank Transfer / Card</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Payment Status</label>
                            <div>
                                <span class="badge {{ $booking->payment_status === 'paid' ? 'bg-success' : 'bg-danger' }} px-3 py-2">
                                    {{ strtoupper($booking->payment_status ?? 'unpaid') }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="card bg-light border-0 mt-4 mb-4">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="text-muted small">Estimated Total Amount</span>
                                    <h4 class="mb-0 text-primary">৳<span id="estimatedAmount">0.00</span></h4>
                                </div>
                                <div class="w-50">
                                    <label class="form-label small mb-1 fw-bold">Total Amount <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text">৳</span>
                                        <input type="number" name="total_amount" id="totalAmount" class="form-control fw-bold h5 mb-0 py-2" step="0.01" min="0" value="{{ old('total_amount', $booking->total_amount) }}" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('banquet.bookings.show', $booking) }}" class="btn btn-light px-4">Cancel</a>
                        <button type="submit" class="btn btn-primary px-5 shadow-sm">
                            <i class="fas fa-save me-1"></i> Update Booking
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white">
                <h5 class="mb-0 small fw-bold text-uppercase">Booking History</h5>
            </div>
            <div class="card-body small">
                <div class="mb-2">Created By: <strong>{{ $booking->createdBy->name ?? 'System' }}</strong></div>
                <div class="mb-2">Created At: <strong>{{ $booking->created_at->format('M d, Y h:i A') }}</strong></div>
                <div>Last Updated: <strong>{{ $booking->updated_at->diffForHumans() }}</strong></div>
            </div>
        </div>

        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body small">
                <div class="alert alert-info border-0 py-2 mb-0">
                    <i class="fas fa-info-circle me-1"></i> Changing the status to <strong>Cancelled</strong> will release the venue slot.
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
    }

    venueSelect.addEventListener('change', calculatePrice);
    startTime.addEventListener('change', calculatePrice);
    endTime.addEventListener('change', calculatePrice);
    
    // Initial calculation
    calculatePrice();
});
</script>
@endsection

