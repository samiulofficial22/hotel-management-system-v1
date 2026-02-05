{{-- NEW – SAFE ADDITION: Departments list (Settings → Departments) --}}
@extends('layouts.app')
@section('title', __('messages.Departments'))
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">{{ __('messages.Departments') }}</h1>
    <a href="{{ route('settings.departments.create') }}" class="btn btn-primary">{{ __('messages.Add Department') }}</a>
</div>
<div class="table-responsive">
    <table class="table table-striped">
        <thead>
            <tr>
                <th>{{ __('messages.Name') }}</th>
                <th>{{ __('messages.Code') }}</th>
                <th>{{ __('messages.Description') }}</th>
                <th>{{ __('messages.Status') }}</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @forelse($departments as $dept)
            <tr>
                <td>{{ $dept->name }}</td>
                <td>{{ $dept->code }}</td>
                <td>{{ Str::limit($dept->description, 40) }}</td>
                <td>
                    @if($dept->status === 'active')
                        <span class="badge bg-success">{{ __('messages.Active') }}</span>
                    @else
                        <span class="badge bg-secondary">{{ __('messages.Inactive') }}</span>
                    @endif
                </td>
                <td>
                    <a href="{{ route('settings.departments.edit', $dept) }}" class="btn btn-sm btn-outline-secondary">{{ __('messages.Edit') }}</a>
                    <form action="{{ route('settings.departments.destroy', $dept) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('messages.Delete?') }}');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-outline-danger">{{ __('messages.Delete') }}</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr><td colspan="5" class="text-muted">{{ __('messages.No departments yet.') }}</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
{{ $departments->links() }}
@endsection
