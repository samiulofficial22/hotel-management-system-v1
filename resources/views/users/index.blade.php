@extends('layouts.app')
@section('title', __('messages.Users'))
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">{{ __('messages.Users') }}</h1>
    <a href="{{ route('users.create') }}" class="btn btn-primary">{{ __('Add User') }}</a>
</div>

<div class="table-responsive">
    <table class="table table-striped align-middle">
        <thead>
            <tr>
                <th></th>
                <th>{{ __('messages.Name') }}</th>
                <th>{{ __('Email') }}</th>
                <th>{{ __('Role') }}</th>
                <th>{{ __('messages.Created at') }}</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @foreach($users as $user)
            <tr>
                <td>
                    <img src="{{ $user->profile_pic_url }}" alt="" class="rounded-circle" width="32" height="32" style="object-fit: cover;">
                </td>
                <td>{{ $user->name }}</td>
                <td>{{ $user->email }}</td>
                <td>
                    @php $roles = $user->roles->pluck('name')->implode(', '); @endphp
                    {{ $roles ?: '-' }}
                </td>
                <td>{{ $user->created_at?->format('Y-m-d H:i') }}</td>
                <td class="text-end">
                    <a href="{{ route('users.edit', $user) }}" class="btn btn-sm btn-outline-secondary">{{ __('Edit') }}</a>
                    @if(auth()->id() !== $user->id)
                    <form action="{{ route('users.destroy', $user) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('messages.Delete?') }}');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-outline-danger">{{ __('Delete') }}</button>
                    </form>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

{{ $users->links() }}
@endsection

