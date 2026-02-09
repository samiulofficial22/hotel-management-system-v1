@extends('layouts.app')
@section('title', __('My profile'))
@section('content')
<h1 class="h3 mb-4">{{ __('My profile') }}</h1>

{{-- Profile picture & welcome (guest-style) --}}
<div class="text-center mb-4 py-4">
    <img src="{{ $user->profile_pic_url }}" alt="{{ $user->name }}" class="rounded-circle shadow-sm border border-3 border-primary" width="120" height="120" style="object-fit: cover;">
    <h2 class="h4 mt-3 mb-1">{{ $user->name }}</h2>
    <p class="text-muted mb-0">{{ $user->email }}</p>
</div>

{{-- Read-only info card (guest-style) --}}
<div class="row g-3 mb-4">
    <div class="col-md-6 col-lg-4">
        <div class="card h-100 shadow-sm">
            <div class="card-header bg-light">{{ __('messages.Personal information') }}</div>
            <div class="card-body">
                <dl class="mb-0 small">
                    <dt class="text-muted">{{ __('messages.Name') }}</dt>
                    <dd class="mb-2">{{ $user->name }}</dd>
                    <dt class="text-muted">{{ __('Email') }}</dt>
                    <dd class="mb-0">{{ $user->email }}</dd>
                </dl>
            </div>
        </div>
    </div>
</div>

<form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <div class="row">
        <div class="col-md-7">
            <div class="card mb-4 shadow-sm">
                <div class="card-header bg-light">{{ __('Profile information') }}</div>
                <div class="card-body">
                    <p class="small text-muted mb-3">{{ auth()->user()->hasRole('Admin') ? __('You can update your name, email and profile picture below.') : __('You can update your name and profile picture below.') }}</p>
                    <div class="mb-3 d-flex align-items-start gap-3">
                        <img src="{{ $user->profile_pic_url }}" alt="" class="rounded-circle flex-shrink-0" width="80" height="80" style="object-fit: cover;">
                        <div class="flex-grow-1">
                            <label class="form-label">{{ __('Profile picture') }} ({{ __('optional') }})</label>
                            <input type="file" name="profile_pic" class="form-control @error('profile_pic') is-invalid @enderror" accept=".jpg,.jpeg,.png">
                            <small class="text-muted">{{ __('JPG or PNG, max 2MB. Leave blank to keep current.') }}</small>
                            @error('profile_pic')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">{{ __('messages.Name') }} <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $user->name) }}" required maxlength="255">
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-0">
                        <label class="form-label">{{ __('Email') }}</label>
                        @if(auth()->user()->hasRole('Admin'))
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $user->email) }}" required>
                            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        @else
                            <input type="text" class="form-control bg-light" value="{{ $user->email }}" readonly disabled>
                            <input type="hidden" name="email" value="{{ $user->email }}">
                            <small class="text-muted">{{ __('messages.Only admin can change email.') }}</small>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-5">
            <div class="card mb-4 shadow-sm">
                <div class="card-header bg-light">{{ __('Change password') }} ({{ __('optional') }})</div>
                <div class="card-body">
                    <p class="text-muted small mb-3">{{ __('Leave blank to keep current password.') }}</p>
                    <div class="mb-3">
                        <label class="form-label">{{ __('New password') }}</label>
                        <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" autocomplete="new-password">
                        @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-0">
                        <label class="form-label">{{ __('Confirm password') }}</label>
                        <input type="password" name="password_confirmation" class="form-control" autocomplete="new-password">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="mb-4">
        <button type="submit" class="btn btn-primary">{{ __('messages.Update') }}</button>
        <a href="{{ route('dashboard') }}" class="btn btn-secondary">{{ __('messages.Cancel') }}</a>
    </div>
</form>
@endsection
