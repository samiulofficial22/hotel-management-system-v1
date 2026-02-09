@extends('layouts.app')
@section('title', __('Edit Role') . ': ' . $role->name)
@section('content')
<h1 class="h3 mb-4">{{ __('Edit Role') }}: {{ $role->name }}</h1>

<form action="{{ route('roles.update', $role) }}" method="POST">
    @csrf
    @method('PUT')
    <div class="mb-4">
        <label class="form-label">{{ __('Role name') }}</label>
        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $role->name) }}" required maxlength="255">
        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="mb-4">
        <label class="form-label">{{ __('Permissions') }}</label>
        <p class="small text-muted mb-2">{{ __('Select the permissions this role should have.') }}</p>
        <div class="border rounded p-3" style="max-height: 400px; overflow-y: auto;">
            @php
                $grouped = $permissions->groupBy(function ($p) {
                    return explode('.', $p->name)[0] ?? 'other';
                });
            @endphp
            @foreach($grouped as $group => $perms)
            <div class="mb-3">
                <span class="small fw-medium text-uppercase text-muted d-block mb-2">{{ $group }}</span>
                <div class="d-flex flex-wrap gap-3">
                    @foreach($perms as $perm)
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="permissions[]" value="{{ $perm->id }}" id="perm{{ $perm->id }}" {{ in_array($perm->id, old('permissions', $rolePermissionIds ?? [])) ? 'checked' : '' }}>
                        <label class="form-check-label small" for="perm{{ $perm->id }}">{{ $perm->name }}</label>
                    </div>
                    @endforeach
                </div>
            </div>
            @endforeach
        </div>
        @error('permissions')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
    </div>

    <button type="submit" class="btn btn-primary">{{ __('messages.Update') }}</button>
    <a href="{{ route('roles.index') }}" class="btn btn-secondary">{{ __('messages.Cancel') }}</a>
</form>
@endsection
