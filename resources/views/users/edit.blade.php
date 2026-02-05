@extends('layouts.app')
@section('title', __('Edit User') . ': ' . $user->name)
@section('content')
<h1 class="h3 mb-4">{{ __('Edit User') }}: {{ $user->name }}</h1>

<form action="{{ route('users.update', $user) }}" method="POST">
    @csrf
    @method('PUT')
    <div class="mb-3">
        <label class="form-label">{{ __('messages.Name') }}</label>
        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $user->name) }}" required maxlength="255">
        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="mb-3">
        <label class="form-label">{{ __('Email') }}</label>
        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $user->email) }}" required>
        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="mb-3">
        <label class="form-label">{{ __('Password') }} ({{ __('optional') }})</label>
        <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" autocomplete="new-password">
        @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
        <small class="text-muted d-block">{{ __('Leave blank to keep current password.') }}</small>
    </div>
    <div class="mb-3">
        <label class="form-label">{{ __('Roles') }}</label>
        <select name="roles[]" class="form-select @error('roles') is-invalid @enderror" multiple>
            @foreach($roles as $id => $name)
                <option value="{{ $id }}" {{ in_array($id, old('roles', $assignedRoles)) ? 'selected' : '' }}>{{ $name }}</option>
            @endforeach
        </select>
        @error('roles')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <button type="submit" class="btn btn-primary">{{ __('messages.Update') }}</button>
    <a href="{{ route('users.index') }}" class="btn btn-secondary">{{ __('messages.Cancel') }}</a>
</form>
@endsection

