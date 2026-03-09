@extends('layouts.app')
@section('title', __('My profile'))
@section('content')
<h1 class="h3 mb-4">{{ __('My profile') }}</h1>

@if(!$guest)
    <div class="alert alert-warning">
        {{ __('No guest profile is linked to your account yet. Please contact the hotel.') }}
    </div>
@else
    {{-- All guest information (read-only) --}}
    <div class="row g-3 mb-4">
        <div class="col-md-6 col-lg-4">
            <div class="card h-100 shadow-sm">
                <div class="card-header bg-light">{{ __('messages.Personal information') }}</div>
                <div class="card-body">
                    <dl class="mb-0 small">
                        <dt class="text-muted">{{ __('messages.First name') }}</dt>
                        <dd class="mb-2">{{ $guest->first_name ?? '-' }}</dd>
                        <dt class="text-muted">{{ __('messages.Last name') }}</dt>
                        <dd class="mb-2">{{ $guest->last_name ?? '-' }}</dd>
                        @if($guest->date_of_birth)
                            <dt class="text-muted">{{ __('messages.Date of birth') }}</dt>
                            <dd class="mb-2">{{ $guest->date_of_birth->format('Y-m-d') }}</dd>
                        @endif
                        @if($guest->nationality)
                            <dt class="text-muted">{{ __('messages.Nationality') }}</dt>
                            <dd class="mb-0">{{ $guest->nationality }}</dd>
                        @endif
                    </dl>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-4">
            <div class="card h-100 shadow-sm">
                <div class="card-header bg-light">{{ __('messages.Contact') }}</div>
                <div class="card-body">
                    <dl class="mb-0 small">
                        <dt class="text-muted">{{ __('messages.Email') }}</dt>
                        <dd class="mb-2">{{ $guest->email ?? $user->email ?? '-' }}</dd>
                        <dt class="text-muted">{{ __('messages.Phone') }}</dt>
                        <dd class="mb-0">{{ $guest->phone ?? '-' }}</dd>
                    </dl>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-4">
            <div class="card h-100 shadow-sm">
                <div class="card-header bg-light">{{ __('messages.Address') }}</div>
                <div class="card-body">
                    <dl class="mb-0 small">
                        @if($guest->address)
                            <dt class="text-muted">{{ __('messages.Address') }}</dt>
                            <dd class="mb-2">{{ $guest->address }}</dd>
                        @endif
                        @if($guest->city)
                            <dt class="text-muted">{{ __('messages.City') }}</dt>
                            <dd class="mb-2">{{ $guest->city }}</dd>
                        @endif
                        @if($guest->country)
                            <dt class="text-muted">{{ __('messages.Country') }}</dt>
                            <dd class="mb-0">{{ $guest->country }}</dd>
                        @endif
                        @if(!$guest->address && !$guest->city && !$guest->country)
                            <dd class="text-muted mb-0">-</dd>
                        @endif
                    </dl>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-4">
            <div class="card h-100 shadow-sm">
                <div class="card-header bg-light">{{ __('messages.ID / NID') }}</div>
                <div class="card-body">
                    <dl class="mb-0 small">
                        @if($guest->id_type)
                            <dt class="text-muted">{{ __('messages.ID type') }}</dt>
                            <dd class="mb-2">{{ $guest->id_type }}</dd>
                        @endif
                        @if($guest->id_number)
                            <dt class="text-muted">{{ __('messages.ID number') }}</dt>
                            <dd class="mb-2">{{ $guest->id_number }}</dd>
                        @endif
                        @if($guest->nid_number)
                            <dt class="text-muted">{{ __('messages.NID number') }}</dt>
                            <dd class="mb-0">{{ $guest->nid_number }}</dd>
                        @endif
                        @if(!$guest->id_type && !$guest->id_number && !$guest->nid_number)
                            <dd class="text-muted mb-0">-</dd>
                        @endif
                    </dl>
                </div>
            </div>
        </div>
    </div>

    @if($guest->notes)
    <div class="card mb-4 shadow-sm">
        <div class="card-header bg-light">{{ __('messages.Notes') }}</div>
        <div class="card-body">
            <p class="mb-0 small">{{ $guest->notes }}</p>
        </div>
    </div>
    @endif

    @if($guest->nid_photo_front || $guest->nid_photo_back)
    <div class="mb-4">
        <h5 class="mb-3">{{ __('messages.NID photos') }}</h5>
        <div class="row g-3">
            @if($guest->nid_photo_front)
            <div class="col-md-6">
                <div class="card border shadow-sm">
                    <div class="card-header small bg-light py-1">{{ __('messages.NID – Front') }}</div>
                    <div class="card-body p-2 text-center">
                        <a href="{{ asset('storage/' . $guest->nid_photo_front) }}" target="_blank" class="d-inline-block">
                            <img src="{{ asset('storage/' . $guest->nid_photo_front) }}" alt="NID Front" class="img-fluid rounded" style="max-height: 280px; object-fit: contain;">
                        </a>
                        <small class="d-block mt-1"><a href="{{ asset('storage/' . $guest->nid_photo_front) }}" target="_blank">{{ __('messages.Open full size') }}</a></small>
                    </div>
                </div>
            </div>
            @endif
            @if($guest->nid_photo_back)
            <div class="col-md-6">
                <div class="card border shadow-sm">
                    <div class="card-header small bg-light py-1">{{ __('messages.NID – Back') }}</div>
                    <div class="card-body p-2 text-center">
                        <a href="{{ asset('storage/' . $guest->nid_photo_back) }}" target="_blank" class="d-inline-block">
                            <img src="{{ asset('storage/' . $guest->nid_photo_back) }}" alt="NID Back" class="img-fluid rounded" style="max-height: 280px; object-fit: contain;">
                        </a>
                        <small class="d-block mt-1"><a href="{{ asset('storage/' . $guest->nid_photo_back) }}" target="_blank">{{ __('messages.Open full size') }}</a></small>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
    @endif

    {{-- Editable profile & password --}}
    <div class="row">
        <div class="col-md-7">
            <div class="card mb-4">
                <div class="card-header">{{ __('Profile information') }}</div>
                <div class="card-body">
                    <p class="small text-muted mb-3">{{ __('You can update name, email, phone, address and related fields below.') }}</p>
                    <form method="POST" action="{{ route('guest.profile.update') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3 d-flex align-items-start gap-3">
                            <img src="{{ $user->profile_pic_url }}" alt="" class="rounded-circle flex-shrink-0" width="80" height="80" style="object-fit: cover;">
                            <div class="flex-grow-1">
                                <label class="form-label">{{ __('Profile picture') }} ({{ __('optional') }})</label>
                                <input type="file" name="profile_pic" class="form-control @error('profile_pic') is-invalid @enderror" accept=".jpg,.jpeg,.png,.webp">
                                <small class="text-muted">{{ __('JPG, PNG or WEBP, max 2MB. Leave blank to keep current.') }}</small>
                                @error('profile_pic')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">{{ __('Name') }}</label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                   value="{{ old('name', $user->name) }}" required maxlength="255">
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">{{ __('Login email') }}</label>
                            <input type="text" class="form-control bg-light" value="{{ $user->email }}" readonly disabled>
                            <small class="text-muted">{{ __('Login email cannot be changed.') }}</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">{{ __('Phone') }}</label>
                            <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror"
                                   value="{{ old('phone', $guest->phone) }}">
                            @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">{{ __('Nationality') }}</label>
                            <input type="text" name="nationality" class="form-control @error('nationality') is-invalid @enderror"
                                   value="{{ old('nationality', $guest->nationality) }}">
                            @error('nationality')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">{{ __('Address') }}</label>
                            <textarea name="address" class="form-control @error('address') is-invalid @enderror" rows="2">{{ old('address', $guest->address) }}</textarea>
                            @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">{{ __('City') }}</label>
                                <input type="text" name="city" class="form-control @error('city') is-invalid @enderror"
                                       value="{{ old('city', $guest->city) }}">
                                @error('city')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">{{ __('Country') }}</label>
                                <input type="text" name="country" class="form-control @error('country') is-invalid @enderror"
                                       value="{{ old('country', $guest->country) }}">
                                @error('country')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary btn-sm">{{ __('Update') }}</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-5">
            <div class="card mb-4">
                <div class="card-header">{{ __('Change password') }}</div>
                <div class="card-body">
                    <form method="POST" action="{{ route('guest.password.update') }}">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">{{ __('New password') }}</label>
                            <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" autocomplete="new-password">
                            @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">{{ __('Confirm password') }}</label>
                            <input type="password" name="password_confirmation" class="form-control" autocomplete="new-password">
                        </div>
                        <button type="submit" class="btn btn-outline-primary btn-sm">{{ __('Update') }}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endif
@endsection

