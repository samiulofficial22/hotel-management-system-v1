@extends(isset($useAppLayout) && $useAppLayout ? 'layouts.app' : 'layouts.public')
@section('title', __('Request a booking') . ' - ' . __('messages.Hotel'))
@section('content')
<div class="{{ isset($useAppLayout) && $useAppLayout ? '' : 'container py-5' }}">
    <h1 class="h3 mb-4">{{ __('Request a booking') }}</h1>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<form method="POST" action="{{ route('booking.request.store') }}">
    @csrf
    @if(isset($guest) && $guest)
        <input type="hidden" name="guest_id" value="{{ $guest->id }}">
    @endif

    <div class="card mb-4">
        <div class="card-header">{{ __('Personal information') }}</div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">{{ __('First name') }} *</label>
                    <input type="text" name="first_name" class="form-control @error('first_name') is-invalid @enderror" value="{{ old('first_name', isset($guest) && $guest ? $guest->first_name : '') }}" required>
                    @error('first_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">{{ __('Last name') }} *</label>
                    <input type="text" name="last_name" class="form-control @error('last_name') is-invalid @enderror" value="{{ old('last_name', isset($guest) && $guest ? $guest->last_name : '') }}" required>
                    @error('last_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">{{ __('Email') }}</label>
                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', isset($guest) && $guest ? $guest->email : '') }}">
                    @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">{{ __('Phone') }}</label>
                    <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone', isset($guest) && $guest ? $guest->phone : '') }}">
                    <small class="text-muted d-block">{{ __('Provide at least email or phone.') }}</small>
                    @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header">{{ __('Stay details') }}</div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">{{ __('Check-in date') }} *</label>
                    <input type="date" name="check_in_date" class="form-control @error('check_in_date') is-invalid @enderror" value="{{ old('check_in_date') }}" required>
                    @error('check_in_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">{{ __('Check-out date') }} *</label>
                    <input type="date" name="check_out_date" class="form-control @error('check_out_date') is-invalid @enderror" value="{{ old('check_out_date') }}" required>
                    @error('check_out_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">{{ __('Room') }} *</label>
                    <select name="room_id" class="form-select @error('room_id') is-invalid @enderror" required>
                        <option value="">{{ __('Select a room') }}</option>
                        @foreach($rooms as $room)
                            <option value="{{ $room->id }}" {{ old('room_id') == $room->id ? 'selected' : '' }}>
                                {{ $room->number }} – {{ $room->roomType->name ?? '' }}
                            </option>
                        @endforeach
                    </select>
                    @error('room_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>
            <div class="row">
                <div class="col-md-3 mb-3">
                    <label class="form-label">{{ __('Adults') }}</label>
                    <input type="number" name="adults" class="form-control @error('adults') is-invalid @enderror" value="{{ old('adults', 1) }}" min="1" max="20">
                    @error('adults')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">{{ __('Children') }}</label>
                    <input type="number" name="children" class="form-control @error('children') is-invalid @enderror" value="{{ old('children', 0) }}" min="0" max="20">
                    @error('children')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">{{ __('Special requests') }}</label>
                    <textarea name="special_requests" class="form-control @error('special_requests') is-invalid @enderror" rows="2">{{ old('special_requests') }}</textarea>
                    @error('special_requests')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>
        </div>
    </div>

    <div class="d-flex align-items-center gap-2">
        <button type="submit" class="btn btn-primary">{{ __('Submit request') }}</button>
        @if(isset($useAppLayout) && $useAppLayout)
            <a href="{{ route('guest.dashboard') }}" class="btn btn-outline-secondary">{{ __('Back to dashboard') }}</a>
        @endif
    </div>
</form>
</div>
@endsection

