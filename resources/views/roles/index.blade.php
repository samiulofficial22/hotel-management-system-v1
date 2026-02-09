@extends('layouts.app')
@section('title', __('Roles'))
@section('content')
<div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
    <h1 class="h3 mb-0">{{ __('Roles') }}</h1>
    @can('roles.manage')
    <a href="{{ route('roles.create') }}" class="btn btn-primary btn-sm">{{ __('Add Role') }}</a>
    @endcan
</div>

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="{{ __('messages.Close') }}"></button>
    </div>
@endif

<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover mb-0 align-middle">
            <thead class="table-light">
                <tr>
                    <th>{{ __('messages.Name') }}</th>
                    <th>{{ __('Users') }}</th>
                    <th>{{ __('Permissions') }}</th>
                    <th class="text-end">{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($roles as $role)
                <tr>
                    <td class="fw-medium">{{ $role->name }}</td>
                    <td>{{ $role->users_count }}</td>
                    <td>{{ $role->permissions_count }}</td>
                    <td class="text-end">
                        <a href="{{ route('roles.edit', $role) }}" class="btn btn-sm btn-outline-primary">{{ __('Edit') }}</a>
                        @if($role->users_count === 0)
                        <form action="{{ route('roles.destroy', $role) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('Are you sure you want to delete this role?') }}');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger">{{ __('Delete') }}</button>
                        </form>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="text-muted text-center py-4">{{ __('No roles found.') }}</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
