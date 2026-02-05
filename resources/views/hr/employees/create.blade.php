@extends('layouts.app')
@section('title', __('Add Employee'))
@section('content')
<h1 class="h3 mb-4">{{ __('Add Employee') }}</h1>

<form method="POST" action="{{ route('hr.employees.store') }}" enctype="multipart/form-data">
    @csrf

    {{-- Personal info --}}
    <div class="card mb-4">
        <div class="card-header">{{ __('Personal information') }}</div>
        <div class="card-body">
            <div class="mb-3">
                <label class="form-label">{{ __('messages.Name') }} <span class="text-danger">*</span></label>
                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required maxlength="200">
                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label class="form-label">{{ __('Phone') }}</label>
                <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone') }}" maxlength="30">
                @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label class="form-label">{{ __('Email') }}</label>
                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}">
                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label class="form-label">{{ __('Photo') }} ({{ __('optional') }})</label>
                <input type="file" name="photo" class="form-control @error('photo') is-invalid @enderror" accept=".jpg,.jpeg,.png">
                <small class="text-muted">{{ __('JPG or PNG, max 2MB') }}</small>
                @error('photo')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label class="form-label">{{ __('NID number') }} ({{ __('optional') }})</label>
                <input type="text" name="nid_number" class="form-control @error('nid_number') is-invalid @enderror" value="{{ old('nid_number') }}" maxlength="50">
                @error('nid_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label class="form-label">{{ __('NID photo') }} ({{ __('optional') }})</label>
                <input type="file" name="nid_photo" class="form-control @error('nid_photo') is-invalid @enderror" accept=".jpg,.jpeg,.png">
                <small class="text-muted">{{ __('JPG or PNG, max 2MB') }}</small>
                @error('nid_photo')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>
    </div>

    {{-- Job info --}}
    <div class="card mb-4">
        <div class="card-header">{{ __('Job information') }}</div>
        <div class="card-body">
            <div class="mb-3">
                <label class="form-label">{{ __('Employee code') }}</label>
                <input type="text" name="employee_code" class="form-control @error('employee_code') is-invalid @enderror" value="{{ old('employee_code') }}" placeholder="{{ __('Auto-generated if left blank') }}" maxlength="30">
                @error('employee_code')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label class="form-label">{{ __('Designation') }}</label>
                <input type="text" name="designation" class="form-control @error('designation') is-invalid @enderror" value="{{ old('designation') }}" maxlength="100">
                @error('designation')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label class="form-label">{{ __('Join date') }}</label>
                <input type="date" name="join_date" class="form-control @error('join_date') is-invalid @enderror" value="{{ old('join_date') }}">
                @error('join_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label class="form-label">{{ __('Status') }}</label>
                <select name="status" class="form-select @error('status') is-invalid @enderror">
                    <option value="">{{ __('messages.Not assigned') }}</option>
                    <option value="active" {{ old('status') === 'active' ? 'selected' : '' }}>{{ __('Active') }}</option>
                    <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>{{ __('Inactive') }}</option>
                </select>
                @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>
    </div>

    {{-- Optional department --}}
    <div class="card mb-4">
        <div class="card-header">{{ __('messages.Department') }} ({{ __('optional') }})</div>
        <div class="card-body">
            <div class="mb-0">
                <label class="form-label">{{ __('messages.Department') }}</label>
                <select name="department_id" class="form-select @error('department_id') is-invalid @enderror">
                    <option value="">{{ __('messages.Not assigned') }}</option>
                    @foreach($departments ?? [] as $d)
                        <option value="{{ $d->id }}" {{ old('department_id') == $d->id ? 'selected' : '' }}>{{ $d->name }} ({{ $d->code }})</option>
                    @endforeach
                </select>
                @error('department_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>
    </div>

    {{-- Optional salary & shift --}}
    <div class="card mb-4">
        <div class="card-header">{{ __('Salary & shift') }} ({{ __('optional') }})</div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">{{ __('Employment type') }}</label>
                    <input type="text" name="employment_type" class="form-control @error('employment_type') is-invalid @enderror" value="{{ old('employment_type') }}" placeholder="e.g. Full-time" maxlength="50">
                    @error('employment_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">{{ __('Salary') }}</label>
                    <input type="number" name="salary" class="form-control @error('salary') is-invalid @enderror" value="{{ old('salary') }}" step="0.01" min="0" placeholder="0">
                    @error('salary')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>
            <div class="mb-0">
                <label class="form-label">{{ __('Shift') }}</label>
                <input type="text" name="shift" class="form-control @error('shift') is-invalid @enderror" value="{{ old('shift') }}" placeholder="e.g. Morning" maxlength="50">
                @error('shift')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>
    </div>

    <div class="mb-4">
        <button type="submit" class="btn btn-primary">{{ __('Create') }}</button>
        <a href="{{ route('hr.employees.index') }}" class="btn btn-secondary">{{ __('Cancel') }}</a>
    </div>
</form>
@endsection
