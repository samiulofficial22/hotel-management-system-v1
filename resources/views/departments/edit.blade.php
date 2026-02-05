{{-- NEW – SAFE ADDITION: Edit Department --}}
@extends('layouts.app')
@section('title', __('messages.Edit') . ': ' . $department->name)
@section('content')
<h1 class="h3 mb-4">{{ __('messages.Edit') }}: {{ $department->name }}</h1>
<form action="{{ route('settings.departments.update', $department) }}" method="POST">
    @csrf
    @method('PUT')
    <div class="mb-3">
        <label class="form-label">{{ __('messages.Name') }}</label>
        <input type="text" name="name" class="form-control" value="{{ old('name', $department->name) }}" required maxlength="100">
    </div>
    <div class="mb-3">
        <label class="form-label">{{ __('messages.Code') }}</label>
        <input type="text" name="code" class="form-control" value="{{ old('code', $department->code) }}" required maxlength="50">
    </div>
    <div class="mb-3">
        <label class="form-label">{{ __('messages.Description') }}</label>
        <textarea name="description" class="form-control" rows="2" maxlength="500">{{ old('description', $department->description) }}</textarea>
    </div>
    <div class="mb-3">
        <label class="form-label">{{ __('messages.Status') }}</label>
        <select name="status" class="form-select">
            <option value="active" {{ old('status', $department->status) === 'active' ? 'selected' : '' }}>{{ __('messages.Active') }}</option>
            <option value="inactive" {{ old('status', $department->status) === 'inactive' ? 'selected' : '' }}>{{ __('messages.Inactive') }}</option>
        </select>
    </div>
    <button type="submit" class="btn btn-primary">{{ __('messages.Update') }}</button>
    <a href="{{ route('settings.departments.index') }}" class="btn btn-secondary">{{ __('messages.Cancel') }}</a>
</form>
@endsection
