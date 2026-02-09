@extends('layouts.app')
@section('title', __('messages.Maintenance'))
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">{{ __('messages.Maintenance Requests') }}</h1>
    @can('maintenance.manage')
        <a href="{{ route('maintenance.create') }}" class="btn btn-primary">{{ __('messages.New Request') }}</a>
    @endcan
</div>

<div class="table-responsive">
<table class="table table-striped table-hover">
<thead class="table-light">
<tr>
    <th>ID</th>
    <th>{{ __('messages.Title') }}</th>
    <th>{{ __('messages.Room') }}</th>
    <th>{{ __('messages.Assigned to') }}</th>
    <th>{{ __('messages.Status') }}</th>
    <th>{{ __('messages.Started at') }}</th>
    <th>{{ __('messages.Resolved at') }}</th>
    <th></th>
</tr>
</thead>
<tbody>
@foreach($requests as $r)
@php
    $tz = config('app.timezone');
    $startedAt = $r->started_at ? $r->started_at->timezone($tz)->format('d M Y, h:i A') : '–';
    $resolvedAt = $r->resolved_at ? $r->resolved_at->timezone($tz)->format('d M Y, h:i A') : '–';
@endphp
<tr>
    <td>{{ ($requests->currentPage() - 1) * $requests->perPage() + $loop->iteration }}</td>
    <td>{{ $r->title }}</td>
    <td>{{ $r->room->number ?? '–' }}</td>
    <td>{{ $r->assignedTo->name ?? '–' }}</td>
    <td>
        @php $cfg = \App\Models\MaintenanceRequest::statusBadgeConfig($r->status); @endphp
        <span class="badge {{ $cfg['class'] }}">{{ __('messages.' . $cfg['label']) }}</span>
    </td>
    <td class="text-nowrap" title="{{ $tz }}">{{ $startedAt }}</td>
    <td class="text-nowrap" title="{{ $tz }}">{{ $resolvedAt }}</td>
    <td>
        <a href="{{ route('maintenance.show', $r) }}" class="btn btn-sm btn-outline-primary">{{ __('messages.View') }}</a>
        @can('maintenance.manage')
            <a href="{{ route('maintenance.edit', $r) }}" class="btn btn-sm btn-outline-secondary">{{ __('messages.Edit') }}</a>
        @endcan
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
