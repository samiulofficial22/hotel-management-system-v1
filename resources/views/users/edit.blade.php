@extends('layouts.app')
@section('title', __('Edit User') . ': ' . $user->name)
@section('content')
<h1 class="h3 mb-4">{{ __('Edit User') }}: {{ $user->name }}</h1>

<form action="{{ route('users.update', $user) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    {{-- Personal information (same card style as employee edit) --}}
    <div class="card mb-4 shadow-sm">
        <div class="card-header bg-light">{{ __('Personal information') }}</div>
        <div class="card-body">
            <div class="mb-3 d-flex align-items-start gap-3">
                <img src="{{ $user->profile_pic_url }}" alt="" class="rounded-circle flex-shrink-0" width="64" height="64" style="object-fit: cover;">
                <div class="flex-grow-1">
                    <label class="form-label">{{ __('Profile picture') }} ({{ __('optional') }})</label>
                    <input type="file" name="profile_pic" class="form-control form-control-sm @error('profile_pic') is-invalid @enderror" accept=".jpg,.jpeg,.png,.webp">
                    <small class="text-muted">{{ __('JPG, PNG or WEBP, max 2MB. Leave blank to keep current.') }}</small>
                    @error('profile_pic')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">{{ __('messages.Name') }} <span class="text-danger">*</span></label>
                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $user->name) }}" required maxlength="255">
                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-0">
                <label class="form-label">{{ __('Email') }} <span class="text-danger">*</span></label>
                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $user->email) }}" required>
                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>
    </div>

    {{-- Roles (same card style as employee Job information) --}}
    <div class="card mb-4 shadow-sm">
        <div class="card-header bg-light">{{ __('Roles') }}</div>
        <div class="card-body">
            @if($canAssignRole ?? auth()->user()?->can('roles.assign'))
                <div class="mb-0">
                    <label class="form-label">{{ __('Assign roles') }}</label>
                    <select name="roles[]" class="form-select @error('roles') is-invalid @enderror" multiple size="6">
                        @foreach($roles as $id => $name)
                            <option value="{{ $id }}" {{ in_array($id, old('roles', $assignedRoles ?? [])) ? 'selected' : '' }}>{{ $name }}</option>
                        @endforeach
                    </select>
                    <small class="text-muted">{{ __('Hold Ctrl/Cmd to select multiple.') }}</small>
                    @error('roles')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            @else
                <p class="form-control-plaintext text-muted mb-0">{{ $user->roles->pluck('name')->implode(', ') ?: '-' }}</p>
            @endif
        </div>
    </div>

    <div class="mb-4">
        <button type="submit" class="btn btn-primary">{{ __('messages.Update') }}</button>
        <a href="{{ route('users.index') }}" class="btn btn-secondary">{{ __('messages.Cancel') }}</a>
    </div>
</form>
@endsection
