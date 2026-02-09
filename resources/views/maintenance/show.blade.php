@extends('layouts.app')
@section('title', __('messages.Maintenance') . ': ' . $request->title)
@section('content')
@php
    $isAssigned = $request->assigned_to && (int) $request->assigned_to === (int) auth()->id();
    $canManage = auth()->user()?->can('maintenance.manage');
@endphp
<div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
    <h1 class="h3 mb-0">{{ __('messages.Maintenance') }}: {{ $request->title }}</h1>
    <div class="d-flex flex-wrap gap-2">
        @if($canManage)
            <a href="{{ route('maintenance.edit', $request) }}" class="btn btn-primary">{{ __('messages.Edit') }}</a>
        @endif
        @if($isAssigned || $canManage)
            @if($request->status === 'open')
                <form action="{{ route('maintenance.start', $request) }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-info">{{ __('messages.Start') }}</button>
                </form>
            @endif
            @if($request->status === 'in_progress')
                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#completeModal">{{ __('messages.Complete') }}</button>
                <div class="modal fade" id="completeModal" tabindex="-1">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <form action="{{ route('maintenance.complete', $request) }}" method="POST">
                                @csrf
                                <div class="modal-header">
                                    <h5 class="modal-title">{{ __('messages.Complete') }} – {{ $request->title }}</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <label class="form-label small">{{ __('messages.How work was done') }} / {{ __('messages.Notes') }}</label>
                                    <textarea name="notes" class="form-control" rows="3" placeholder="{{ __('messages.What was done (optional)...') }}">{{ $request->resolution_notes }}</textarea>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('messages.Cancel') }}</button>
                                    <button type="submit" class="btn btn-success">{{ __('messages.Mark complete') }}</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            @endif
        @endif
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
                    @php $tz = config('app.timezone'); @endphp
                    <tr>
                        <td class="text-muted">{{ __('messages.Created at') }}</td>
                        <td>{{ $request->created_at->timezone($tz)->format('d M Y, h:i A') }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">{{ __('messages.Assigned to') }}</td>
                        <td>{{ $request->assignedTo->name ?? '–' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">{{ __('messages.Started at') }}</td>
                        <td>{{ $request->started_at ? $request->started_at->timezone($tz)->format('d M Y, h:i A') : '–' }}</td>
                    </tr>
                    @if($request->status === 'resolved' || $request->resolved_at)
                    <tr>
                        <td class="text-muted">{{ __('messages.Resolved by') }}</td>
                        <td>{{ $request->resolvedBy->name ?? ($request->assignedTo->name ?? '–') }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">{{ __('messages.Resolved at') }}</td>
                        <td>{{ $request->resolved_at ? $request->resolved_at->timezone($tz)->format('d M Y, h:i A') : '–' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted align-top">{{ __('messages.How work was done') }}</td>
                        <td>
                            @if($request->resolution_notes)
                                {{ nl2br(e($request->resolution_notes)) }}
                            @else
                                –
                            @endif
                            @if($isAssigned || $canManage)
                                <button type="button" class="btn btn-sm btn-outline-secondary ms-2 mt-1" data-bs-toggle="modal" data-bs-target="#editNotesModal">{{ __('messages.Edit') }}</button>
                                <div class="modal fade" id="editNotesModal" tabindex="-1">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content">
                                            <form action="{{ route('maintenance.notes.update', $request) }}" method="POST">
                                                @csrf
                                                @method('PATCH')
                                                <div class="modal-header">
                                                    <h5 class="modal-title">{{ __('messages.Edit completion notes') }} – {{ $request->title }}</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <label class="form-label small">{{ __('messages.How work was done') }}</label>
                                                    <textarea name="notes" class="form-control" rows="4">{{ $request->resolution_notes }}</textarea>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('messages.Cancel') }}</button>
                                                    <button type="submit" class="btn btn-primary">{{ __('messages.Update') }}</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </td>
                    </tr>
                    @endif
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
