@extends('layouts.app')
@section('title', __('messages.Maintenance') . ': ' . $request->title)
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">{{ __('messages.Maintenance') }}: {{ $request->title }}</h1>
    <div>
        <a href="{{ route('maintenance.edit', $request) }}" class="btn btn-primary">{{ __('messages.Edit') }}</a>
        <a href="{{ route('maintenance.index') }}" class="btn btn-outline-secondary">{{ __('messages.Back to List') }}</a>
    </div>
</div>

<div class="row">
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-light py-2"><strong>{{ __('messages.Request Details') }}</strong></div>
            <div class="card-body">
                <p class="mb-2"><strong>{{ __('messages.Room') }}:</strong> {{ $request->room->number ?? 'N/A' }}</p>
                <p class="mb-2"><strong>{{ __('messages.Priority') }}:</strong> {{ __('messages.' . ucfirst($request->priority)) }}</p>
                @php $cfg = \App\Models\MaintenanceRequest::statusBadgeConfig($request->status); @endphp
                <p class="mb-2"><strong>{{ __('messages.Status') }}:</strong> <span class="badge {{ $cfg['class'] }}">{{ __('messages.' . $cfg['label']) }}</span></p>
                <p class="mb-0"><strong>{{ __('messages.Description') }}:</strong><br>{{ $request->description ?? '–' }}</p>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-light py-2"><strong>{{ __('messages.How work was done') }}</strong></div>
            <div class="card-body">
                <table class="table table-sm table-borderless mb-0">
                    <tr>
                        <td class="text-muted" style="width: 140px;">{{ __('messages.Reported by') }}</td>
                        <td>{{ $request->reportedBy->name ?? '–' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">{{ __('messages.Created at') }}</td>
                        <td>{{ $request->created_at->format('d M Y, H:i') }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">{{ __('messages.Assigned to') }}</td>
                        <td>{{ $request->assignedTo->name ?? '–' }}</td>
                    </tr>
                    @if($request->status === 'resolved' || $request->resolved_at)
                    <tr>
                        <td class="text-muted">{{ __('messages.Resolved by') }}</td>
                        <td>{{ $request->resolvedBy->name ?? ($request->assignedTo->name ?? '–') }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">{{ __('messages.Resolved at') }}</td>
                        <td>{{ $request->resolved_at ? $request->resolved_at->format('d M Y, H:i') : '–' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted align-top">{{ __('messages.How work was done') }}</td>
                        <td>{{ $request->resolution_notes ? nl2br(e($request->resolution_notes)) : '–' }}</td>
                    </tr>
                    @endif
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
