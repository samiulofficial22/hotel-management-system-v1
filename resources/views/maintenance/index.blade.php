@extends('layouts.app')
@section('title', __('messages.Maintenance'))
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">{{ __('messages.Maintenance Requests') }}</h1>
    <a href="{{ route('maintenance.create') }}" class="btn btn-primary">{{ __('messages.New Request') }}</a>
</div>

<div class="table-responsive">
<table class="table table-striped table-hover">
<thead class="table-light">
<tr>
    <th>{{ __('messages.Title') }}</th>
    <th>{{ __('messages.Room') }}</th>
    <th>{{ __('messages.Assigned to') }}</th>
    <th>{{ __('messages.Status') }}</th>
    <th>{{ __('messages.Resolved at') }}</th>
    <th></th>
</tr>
</thead>
<tbody>
@foreach($requests as $r)
<tr>
    <td>{{ $r->title }}</td>
    <td>{{ $r->room->number ?? '–' }}</td>
    <td>{{ $r->assignedTo->name ?? '–' }}</td>
    <td>
        @php $cfg = \App\Models\MaintenanceRequest::statusBadgeConfig($r->status); @endphp
        <span class="badge {{ $cfg['class'] }}">{{ __('messages.' . $cfg['label']) }}</span>
    </td>
    <td>{{ $r->resolved_at ? $r->resolved_at->format('d M Y, H:i') : '–' }}</td>
    <td>
        <a href="{{ route('maintenance.show', $r) }}" class="btn btn-sm btn-outline-primary">{{ __('messages.View') }}</a>
        <a href="{{ route('maintenance.edit', $r) }}" class="btn btn-sm btn-outline-secondary">{{ __('messages.Edit') }}</a>
    </td>
</tr>
@endforeach
</tbody>
</table>
</div>
@if($requests->isEmpty())
<p class="text-muted">{{ __('messages.No maintenance requests.') }}</p>
@endif
{{ $requests->links() }}
@endsection
