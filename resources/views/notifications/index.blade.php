@extends('layouts.app')
@section('title', __('messages.Notifications'))
@section('content')
<h1 class="h3 mb-4">{{ __('messages.Notifications') }}</h1>

@if($notifications->isEmpty())
    <p class="text-muted">{{ __('No notifications.') }}</p>
@else
    <div class="list-group">
        @foreach($notifications as $n)
            @php $data = $n->data ?? []; @endphp
            <a href="{{ $data['url'] ?? '#' }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-start {{ $n->read_at ? '' : 'list-group-item-primary' }}">
                <div class="ms-2 me-auto">
                    <div class="fw-semibold">{{ $data['message'] ?? __('Notification') }}</div>
                    <small class="text-muted">{{ $n->created_at?->diffForHumans() }}</small>
                </div>
            </a>
        @endforeach
    </div>
    {{ $notifications->links() }}
@endif
@endsection
