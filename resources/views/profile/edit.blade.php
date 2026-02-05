@extends('layouts.app')
@section('title', __('Edit Profile'))
@section('content')
<h1 class="h3 mb-4">{{ __('Edit Profile') }}</h1>

<form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <div class="card mb-4">
        <div class="card-header">{{ __('Personal information') }}</div>
        <div class="card-body">
            <div class="mb-3 d-flex align-items-start gap-3">
                <img src="{{ $user->profile_pic_url }}" alt="" class="rounded-circle flex-shrink-0" width="80" height="80" style="object-fit: cover;">
                <div class="flex-grow-1">
                    <label class="form-label">{{ __('Profile picture') }} ({{ __('optional') }})</label>
                    <input type="file" name="profile_pic" class="form-control @error('profile_pic') is-invalid @enderror" accept=".jpg,.jpeg,.png">
                    <small class="text-muted">{{ __('JPG or PNG, max 2MB. Leave blank to keep current.') }}</small>
                    @error('profile_pic')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">{{ __('messages.Name') }} <span class="text-danger">*</span></label>
                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $user->name) }}" required maxlength="255">
                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label class="form-label">{{ __('Email') }} <span class="text-danger">*</span></label>
                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $user->email) }}" required>
                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header">{{ __('Change password') }} ({{ __('optional') }})</div>
        <div class="card-body">
            <p class="text-muted small">{{ __('Leave blank to keep current password.') }}</p>
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

    <div class="mb-4">
        <button type="submit" class="btn btn-primary">{{ __('Update') }}</button>
        <a href="{{ route('dashboard') }}" class="btn btn-secondary">{{ __('Cancel') }}</a>
    </div>
</form>
@endsection
