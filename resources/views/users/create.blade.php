@extends('layouts.app')
@section('title', __('Add User'))
@section('content')
<h1 class="h3 mb-4">{{ __('Add User') }}</h1>

<form action="{{ route('users.store') }}" method="POST">
    @csrf
    <div class="mb-3">
        <label class="form-label">{{ __('messages.Name') }}</label>
        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required maxlength="255">
        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="mb-3">
        <label class="form-label">{{ __('Email') }}</label>
        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required>
        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="mb-3">
        <label class="form-label">{{ __('Password') }}</label>
        <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" required autocomplete="new-password">
        @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="mb-3">
        <label class="form-label">{{ __('Confirm password') }}</label>
        <input type="password" name="password_confirmation" class="form-control" required autocomplete="new-password">
    </div>
    @can('roles.assign')
    <div class="mb-3">
        <label class="form-label">{{ __('Roles') }}</label>
        <select name="roles[]" class="form-select @error('roles') is-invalid @enderror" multiple>
            @foreach($roles as $id => $name)
                <option value="{{ $id }}" {{ in_array($id, old('roles', [])) ? 'selected' : '' }}>{{ $name }}</option>
            @endforeach
        </select>
        @error('roles')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    @endcan
    <button type="submit" class="btn btn-primary">{{ __('Create') }}</button>
    <a href="{{ route('users.index') }}" class="btn btn-secondary">{{ __('messages.Cancel') }}</a>
</form>
@endsection

