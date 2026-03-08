@extends('layouts.app')
@section('title', 'Edit Booking')
@section('content')
<h1 class="h3 mb-4">Edit Booking</h1>
<form action="{{ route('bookings.update', $booking) }}" method="POST">
    @csrf
    @method('PUT')
    <div class="row g-3">
        <div class="col-md-6"><label class="form-label">Guest</label><select name="guest_id" class="form-select" required>@foreach($guests as $g)<option value="{{ $g->id }}" {{ old('guest_id', $booking->guest_id) == $g->id ? 'selected' : '' }}>{{ $g->full_name }}</option>@endforeach</select></div>
        <div class="col-md-6"><label class="form-label">Room</label><select name="room_id" id="roomSelect" class="form-select" required>@foreach($rooms as $r)<option value="{{ $r->id }}" data-rate="{{ $r->roomType->base_rate ?? 0 }}" {{ old('room_id', $booking->room_id) == $r->id ? 'selected' : '' }}>{{ $r->number }} ({{ $r->roomType->name ?? '' }}) - {{ money($r->roomType->base_rate ?? 0) }}</option>@endforeach</select></div>
        <div class="col-md-6"><label class="form-label">Check-in Date</label><input type="date" name="check_in_date" id="checkInDate" class="form-control" value="{{ old('check_in_date', $booking->check_in_date->format('Y-m-d')) }}" required></div>
        <div class="col-md-6"><label class="form-label">Check-out Date</label><input type="date" name="check_out_date" id="checkOutDate" class="form-control" value="{{ old('check_out_date', $booking->check_out_date->format('Y-m-d')) }}" required></div>
        <div class="col-md-6"><label class="form-label">Room Rate</label><input type="number" step="0.01" name="room_rate" id="roomRateInput" class="form-control" value="{{ old('room_rate', $booking->room_rate) }}" required></div>
        <div class="col-12"><button type="submit" class="btn btn-primary">Update</button> <a href="{{ route('bookings.show', $booking) }}" class="btn btn-secondary">Cancel</a></div>
    </div>
</form>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const roomSelect = document.getElementById('roomSelect');
        const rateInput = document.getElementById('roomRateInput');
        const checkInInput = document.getElementById('checkInDate');
        const checkOutInput = document.getElementById('checkOutDate');
        const currentBookingId = '{{ $booking->id }}';
        const currentRoomId = '{{ $booking->room_id }}';
        
        roomSelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            if(selectedOption && selectedOption.dataset.rate) {
                rateInput.value = selectedOption.dataset.rate;
            } else {
                rateInput.value = '';
            }
        });

        function fetchAvailableRooms() {
            const checkIn = checkInInput.value;
            const checkOut = checkOutInput.value;

            if (checkIn && checkOut) {
                const previousSelection = roomSelect.value;
                roomSelect.innerHTML = '<option value="">Searching for available rooms...</option>';
                roomSelect.disabled = true;

                fetch(`{{ route('api.rooms.available') }}?check_in_date=${encodeURIComponent(checkIn)}&check_out_date=${encodeURIComponent(checkOut)}&exclude_booking_id=${currentBookingId}`)
                    .then(res => res.json())
                    .then(data => {
                        roomSelect.innerHTML = '<option value="">-- Select Room --</option>';
                        roomSelect.disabled = false;
                        
                        if(data.length === 0) {
                            roomSelect.innerHTML += '<option value="" disabled>No rooms available for these dates</option>';
                        } else {
                            data.forEach(room => {
                                const option = document.createElement('option');
                                option.value = room.id;
                                option.dataset.rate = room.base_rate;
                                option.textContent = `${room.number} (${room.type}) - ${room.formatted_rate}`;
                                if (room.id == previousSelection || (previousSelection == "" && room.id == currentRoomId)) {
                                    option.selected = true;
                                }
                                roomSelect.appendChild(option);
                            });
                        }
                    })
                    .catch(err => {
                        roomSelect.disabled = false;
                        roomSelect.innerHTML = '<option value="">-- Error fetching rooms --</option>';
                        console.error("Error fetching rooms:", err);
                    });
            }
        }

        checkInInput.addEventListener('change', fetchAvailableRooms);
        checkOutInput.addEventListener('change', fetchAvailableRooms);
    });
</script>
@endpush

@endsection
