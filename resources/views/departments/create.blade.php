{{-- NEW – SAFE ADDITION: Add Department --}}
@extends('layouts.app')
@section('title', __('messages.Add Department'))
@section('content')
<h1 class="h3 mb-4">{{ __('messages.Add Department') }}</h1>
<form action="{{ route('settings.departments.store') }}" method="POST">
    @csrf
    <div class="mb-3">
        <label class="form-label">{{ __('messages.Name') }}</label>
        <input type="text" name="name" class="form-control" value="{{ old('name') }}" required maxlength="100">
    </div>
    <div class="mb-3">
        <label class="form-label">{{ __('messages.Code') }}</label>
        <input type="text" name="code" class="form-control" value="{{ old('code') }}" required maxlength="50" placeholder="e.g. HR, IT">
    </div>
    <div class="mb-3">
        <label class="form-label">{{ __('messages.Description') }}</label>
        <textarea name="description" class="form-control" rows="2" maxlength="500">{{ old('description') }}</textarea>
    </div>
    <div class="mb-3">
        <label class="form-label">{{ __('messages.Status') }}</label>
        <select name="status" class="form-select">
            <option value="active" {{ old('status', 'active') === 'active' ? 'selected' : '' }}>{{ __('messages.Active') }}</option>
            <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>{{ __('messages.Inactive') }}</option>
        </select>
    </div>
    <button type="submit" class="btn btn-primary">{{ __('messages.Create') }}</button>
    <a href="{{ route('settings.departments.index') }}" class="btn btn-secondary">{{ __('messages.Cancel') }}</a>
</form>
@endsection
