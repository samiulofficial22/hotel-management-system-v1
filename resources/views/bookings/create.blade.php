@extends('layouts.app')
@section('title', 'New Booking')
@section('content')
<h1 class="h3 mb-4">New Booking</h1>
<form action="{{ route('bookings.store') }}" method="POST">
    @csrf
    
    <div class="mb-3 position-relative">
        <label class="form-label d-flex justify-content-between align-items-end">
            <span>Guest</span>
            <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#quickAddGuestModal">
                <i class="bi bi-person-plus"></i> Add New Guest
            </button>
        </label>
        <div class="input-group">
            <span class="input-group-text"><i class="bi bi-search"></i></span>
            <input type="text" id="guestSearchInput" class="form-control" placeholder="Search by name, phone, email, or ID..." autocomplete="off">
        </div>
        <input type="hidden" name="guest_id" id="selectedGuestId" required>
        <div id="guestSearchResults" class="dropdown-menu w-100 shadow-sm" style="display: none; position: absolute; max-height: 300px; overflow-y: auto; z-index: 1050;"></div>
        
        <div id="selectedGuestDisplay" class="alert alert-success mt-2 p-2 d-none align-items-center">
            <i class="bi bi-check-circle-fill me-2"></i>
            <span id="selectedGuestName" class="fw-bold"></span>
            <span id="selectedGuestPhone" class="ms-2 text-muted small"></span>
            <button type="button" class="btn-close btn-sm ms-auto" id="clearGuestSelection" aria-label="Close"></button>
        </div>
    </div>

    <div class="mb-3"><label class="form-label">Room</label>
        <select name="room_id" id="roomSelect" class="form-select" required>
            <option value="">-- Select Room --</option>
            @foreach($rooms as $r)
            <option value="{{ $r->id }}" data-rate="{{ $r->roomType->base_rate ?? 0 }}">
                {{ $r->number }} ({{ $r->roomType->name ?? '' }}) - {{ money($r->roomType->base_rate ?? 0) }}
            </option>
            @endforeach
        </select>
    </div>
    <div class="mb-3"><label class="form-label">Check-in Date</label><input type="date" name="check_in_date" id="checkInDate" class="form-control" value="{{ old('check_in_date') }}" required></div>
    <div class="mb-3"><label class="form-label">Check-out Date</label><input type="date" name="check_out_date" id="checkOutDate" class="form-control" value="{{ old('check_out_date') }}" required></div>
    <div class="mb-3"><label class="form-label">Room Rate (Per Night)</label><input type="number" step="0.01" name="room_rate" id="roomRateInput" class="form-control" value="{{ old('room_rate') }}" required></div>
    <div class="mb-3"><label class="form-label">Booking Type</label><select name="booking_type" class="form-select"><option value="advance">Advance</option><option value="walk_in">Walk-in</option></select></div>
    <button type="submit" class="btn btn-primary">Create Booking</button>
    <a href="{{ route('bookings.index') }}" class="btn btn-secondary">Cancel</a>
</form>

<!-- Quick Add Guest Modal -->
<div class="modal fade" id="quickAddGuestModal" tabindex="-1" aria-labelledby="quickAddGuestModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="quickAddGuestModalLabel">Quick Add Guest</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="quickAddGuestForm">
            @csrf
            <div id="quickAddError" class="alert alert-danger d-none small"></div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">First Name *</label>
                    <input type="text" name="first_name" class="form-control" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Last Name *</label>
                    <input type="text" name="last_name" class="form-control" required>
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">Phone Number</label>
                <input type="text" name="phone" class="form-control">
            </div>
            <div class="mb-3">
                <label class="form-label">Email Address</label>
                <input type="email" name="email" class="form-control">
            </div>
            <div class="mb-3">
                <label class="form-label">National ID / Passport</label>
                <input type="text" name="nid_number" class="form-control">
            </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-primary" id="saveQuickGuestBtn">Save & Select</button>
      </div>
    </div>
  </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const roomSelect = document.getElementById('roomSelect');
        const rateInput = document.getElementById('roomRateInput');
        
        roomSelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            if(selectedOption && selectedOption.dataset.rate) {
                rateInput.value = selectedOption.dataset.rate;
            } else {
                rateInput.value = '';
            }
        });

        // Guest Search Logic
        const searchInput = document.getElementById('guestSearchInput');
        const searchResults = document.getElementById('guestSearchResults');
        const guestIdInput = document.getElementById('selectedGuestId');
        const displayDiv = document.getElementById('selectedGuestDisplay');
        const displayName = document.getElementById('selectedGuestName');
        const displayPhone = document.getElementById('selectedGuestPhone');
        const clearBtn = document.getElementById('clearGuestSelection');
        let searchTimeout;

        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            const query = this.value;
            
            if (query.length < 2) {
                searchResults.style.display = 'none';
                return;
            }

            searchTimeout = setTimeout(() => {
                fetch(`{{ route('api.guests.search') }}?q=${encodeURIComponent(query)}`)
                    .then(res => res.json())
                    .then(data => {
                        searchResults.innerHTML = '';
                        if (data.length === 0) {
                            searchResults.innerHTML = '<div class="p-3 text-muted">No guests found. Click "Add New Guest".</div>';
                        } else {
                            data.forEach(guest => {
                                const btn = document.createElement('button');
                                btn.type = 'button';
                                btn.className = 'dropdown-item py-2 border-bottom';
                                btn.innerHTML = `<strong>${guest.name}</strong> <span class="text-muted small ms-2">${guest.phone}</span>`;
                                btn.addEventListener('click', () => selectGuest(guest));
                                searchResults.appendChild(btn);
                            });
                        }
                        searchResults.style.display = 'block';
                    });
            }, 300);
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (!searchInput.contains(e.target) && !searchResults.contains(e.target)) {
                searchResults.style.display = 'none';
            }
        });

        function selectGuest(guest) {
            guestIdInput.value = guest.id;
            displayName.textContent = guest.name;
            displayPhone.textContent = `(${guest.phone})`;
            
            searchInput.value = '';
            searchResults.style.display = 'none';
            searchInput.parentElement.classList.add('d-none');
            displayDiv.classList.remove('d-none');
            displayDiv.classList.add('d-flex');
        }

        clearBtn.addEventListener('click', function() {
            guestIdInput.value = '';
            displayDiv.classList.add('d-none');
            displayDiv.classList.remove('d-flex');
            searchInput.parentElement.classList.remove('d-none');
            searchInput.focus();
        });

        // Quick Add Guest Logic
        const saveBtn = document.getElementById('saveQuickGuestBtn');
        const form = document.getElementById('quickAddGuestForm');
        const errorDiv = document.getElementById('quickAddError');
        const modalEl = document.getElementById('quickAddGuestModal');
        const modal = new bootstrap.Modal(modalEl);

        saveBtn.addEventListener('click', function() {
            // check required fields natively first
            if(!form.checkValidity()) {
                form.reportValidity();
                return;
            }

            const formData = new FormData(form);
            saveBtn.disabled = true;
            saveBtn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Saving...';
            errorDiv.classList.add('d-none');

            fetch(`{{ route('api.guests.quick-store') }}`, {
                method: 'POST',
                body: formData,
                headers: {
                    'Accept': 'application/json'
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    selectGuest(data.guest);
                    modal.hide();
                    form.reset();
                } else {
                    let errStr = Object.values(data.errors).flat().join('<br>');
                    errorDiv.innerHTML = errStr;
                    errorDiv.classList.remove('d-none');
                }
            })
            .catch(err => {
                errorDiv.innerHTML = 'An unexpected error occurred.';
                errorDiv.classList.remove('d-none');
            })
            .finally(() => {
                saveBtn.disabled = false;
                saveBtn.innerText = 'Save & Select';
            });
        });
        
        // Ensure modal closing resets errors
        modalEl.addEventListener('hidden.bs.modal', function () {
            errorDiv.classList.add('d-none');
            form.reset();
        });

        // Room loading based on dates
        const checkInInput = document.getElementById('checkInDate');
        const checkOutInput = document.getElementById('checkOutDate');

        function fetchAvailableRooms() {
            const checkIn = checkInInput.value;
            const checkOut = checkOutInput.value;

            if (checkIn && checkOut) {
                roomSelect.innerHTML = '<option value="">Searching for available rooms...</option>';
                roomSelect.disabled = true;

                fetch(`{{ route('api.rooms.available') }}?check_in_date=${encodeURIComponent(checkIn)}&check_out_date=${encodeURIComponent(checkOut)}`)
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
                                roomSelect.appendChild(option);
                            });
                        }
                    })
                    .catch(err => {
                        roomSelect.disabled = false;
                        roomSelect.innerHTML = '<option value="">-- Error fetching rooms --</option>';
                        console.error("Error fetching rooms:", err);
                    });
            } else {
                roomSelect.innerHTML = '<option value="">-- Complete Check-in & Check-out Dates First --</option>';
                rateInput.value = '';
            }
        }

        checkInInput.addEventListener('change', fetchAvailableRooms);
        checkOutInput.addEventListener('change', fetchAvailableRooms);

        // Run once on load to initialize drop down state
        fetchAvailableRooms();
    });
</script>
@endpush
@endsection
