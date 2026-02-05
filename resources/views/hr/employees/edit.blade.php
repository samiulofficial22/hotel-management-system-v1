@extends('layouts.app')
@section('title', __('Edit Employee'))
@section('content')
<h1 class="h3 mb-4">{{ __('Edit Employee') }}: {{ $employee->display_name }}</h1>

<form method="POST" action="{{ route('hr.employees.update', $employee) }}" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    {{-- Personal info --}}
    <div class="card mb-4">
        <div class="card-header">{{ __('Personal information') }}</div>
        <div class="card-body">
            <div class="mb-3">
                <label class="form-label">{{ __('messages.Name') }} <span class="text-danger">*</span></label>
                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $employee->name ?? $employee->full_name) }}" required maxlength="200">
                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label class="form-label">{{ __('Phone') }}</label>
                <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone', $employee->phone) }}" maxlength="30">
                @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label class="form-label">{{ __('Email') }}</label>
                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $employee->email) }}">
                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label class="form-label">{{ __('Photo') }} ({{ __('optional') }})</label>
                <div class="d-flex align-items-center gap-3">
                    <img src="{{ $employee->photo_url }}" alt="" class="rounded-circle" width="48" height="48" style="object-fit: cover;">
                    <input type="file" name="photo" class="form-control @error('photo') is-invalid @enderror" accept=".jpg,.jpeg,.png">
                </div>
                <small class="text-muted">{{ __('JPG or PNG, max 2MB. Leave blank to keep current photo.') }}</small>
                @error('photo')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label class="form-label">{{ __('NID number') }} ({{ __('optional') }})</label>
                <input type="text" name="nid_number" class="form-control @error('nid_number') is-invalid @enderror" value="{{ old('nid_number', $employee->nid_number) }}" maxlength="50">
                @error('nid_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label class="form-label">{{ __('NID photo') }} ({{ __('optional') }})</label>
                @if($employee->nid_photo_url)
                    <div class="mb-2">
                        <img src="{{ $employee->nid_photo_url }}" alt="NID" class="img-thumbnail" style="max-height: 120px;">
                        <small class="d-block text-muted">{{ __('Current NID photo') }}</small>
                    </div>
                @endif
                <input type="file" name="nid_photo" class="form-control @error('nid_photo') is-invalid @enderror" accept=".jpg,.jpeg,.png">
                <small class="text-muted">{{ __('JPG or PNG, max 2MB. Leave blank to keep current.') }}</small>
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
                <input type="text" name="employee_code" class="form-control @error('employee_code') is-invalid @enderror" value="{{ old('employee_code', $employee->employee_code ?? $employee->employee_number) }}" maxlength="30">
                @error('employee_code')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label class="form-label">{{ __('Designation') }}</label>
                <input type="text" name="designation" class="form-control @error('designation') is-invalid @enderror" value="{{ old('designation', $employee->designation) }}" maxlength="100">
                @error('designation')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label class="form-label">{{ __('Join date') }}</label>
                <input type="date" name="join_date" class="form-control @error('join_date') is-invalid @enderror" value="{{ old('join_date', $employee->join_date?->format('Y-m-d')) }}">
                @error('join_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label class="form-label">{{ __('Status') }}</label>
                <select name="status" class="form-select @error('status') is-invalid @enderror">
                    <option value="">{{ __('messages.Not assigned') }}</option>
                    <option value="active" {{ old('status', $employee->status ?? ($employee->is_active ? 'active' : 'inactive')) === 'active' ? 'selected' : '' }}>{{ __('Active') }}</option>
                    <option value="inactive" {{ old('status', $employee->status ?? ($employee->is_active ? 'active' : 'inactive')) === 'inactive' ? 'selected' : '' }}>{{ __('Inactive') }}</option>
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
                    @foreach($departments as $d)
                        <option value="{{ $d->id }}" {{ old('department_id', $employee->department_id) == $d->id ? 'selected' : '' }}>{{ $d->name }} ({{ $d->code }})</option>
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
                    <input type="text" name="employment_type" class="form-control @error('employment_type') is-invalid @enderror" value="{{ old('employment_type', $employee->employment_type) }}" maxlength="50">
                    @error('employment_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">{{ __('Salary') }}</label>
                    <input type="number" name="salary" class="form-control @error('salary') is-invalid @enderror" value="{{ old('salary', $employee->salary ?? $employee->base_salary) }}" step="0.01" min="0">
                    @error('salary')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>
            <div class="mb-0">
                <label class="form-label">{{ __('Shift') }}</label>
                <input type="text" name="shift" class="form-control @error('shift') is-invalid @enderror" value="{{ old('shift', $employee->shift) }}" maxlength="50">
                @error('shift')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>
    </div>

    <div class="mb-4">
        <button type="submit" class="btn btn-primary">{{ __('Update') }}</button>
        <a href="{{ route('hr.employees.show', $employee) }}" class="btn btn-secondary">{{ __('Cancel') }}</a>
    </div>
</form>
@endsection
