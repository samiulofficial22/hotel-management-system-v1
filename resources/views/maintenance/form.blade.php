@extends('layouts.app')
@section('title', $request ? __('messages.Edit') . ': ' . $request->title : __('messages.New Maintenance Request'))
@section('content')
<h1 class="h3 mb-4">{{ $request ? __('messages.Edit') . ': ' . $request->title : __('messages.New Maintenance Request') }}</h1>
<form method="POST" action="{{ $request ? route('maintenance.update', $request) : route('maintenance.store') }}">
    @csrf
    @if($request)
        @method('PUT')
    @endif
    <div class="row g-3">
        <div class="col-md-6">
            <label class="form-label">{{ __('messages.Room') }} ({{ __('messages.optional') }})</label>
            <select name="room_id" class="form-select">
                <option value="">{{ __('messages.None') }}</option>
                @foreach($rooms as $r)
                    <option value="{{ $r->id }}" {{ old('room_id', $request?->room_id) == $r->id ? 'selected' : '' }}>{{ $r->number }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-6">
            <label class="form-label">{{ __('messages.Department') }} ({{ __('messages.optional') }})</label>
            <select name="department_id" class="form-select">
                <option value="">{{ __('messages.Not assigned') }}</option>
                @foreach($departments ?? [] as $d)
                    <option value="{{ $d->id }}" {{ old('department_id', $request?->department_id) == $d->id ? 'selected' : '' }}>{{ $d->name }} ({{ $d->code }})</option>
                @endforeach
            </select>
        </div>
        <div class="col-12">
            <label class="form-label">{{ __('messages.Title') }}</label>
            <input type="text" name="title" class="form-control" value="{{ old('title', $request?->title) }}" required>
        </div>
        <div class="col-12">
            <label class="form-label">{{ __('messages.Description') }}</label>
            <textarea name="description" class="form-control" rows="2">{{ old('description', $request?->description) }}</textarea>
        </div>
        <div class="col-md-6">
            <label class="form-label">{{ __('messages.Priority') }}</label>
            <select name="priority" class="form-select">
                <option value="low" {{ old('priority', $request?->priority ?? 'normal') == 'low' ? 'selected' : '' }}>{{ __('messages.Low') }}</option>
                <option value="normal" {{ old('priority', $request?->priority ?? 'normal') == 'normal' ? 'selected' : '' }}>{{ __('messages.Normal') }}</option>
                <option value="high" {{ old('priority', $request?->priority) == 'high' ? 'selected' : '' }}>{{ __('messages.High') }}</option>
                <option value="urgent" {{ old('priority', $request?->priority) == 'urgent' ? 'selected' : '' }}>{{ __('messages.Urgent') }}</option>
            </select>
        </div>
        <div class="col-md-6">
            <label class="form-label">{{ __('messages.Status') }}</label>
            <select name="status" class="form-select">
                <option value="open" {{ old('status', $request?->status ?? 'open') == 'open' ? 'selected' : '' }}>{{ __('messages.Open') }}</option>
                <option value="in_progress" {{ old('status', $request?->status) == 'in_progress' ? 'selected' : '' }}>{{ __('messages.In Progress') }}</option>
                <option value="resolved" {{ old('status', $request?->status) == 'resolved' ? 'selected' : '' }}>{{ __('messages.Resolved') }}</option>
                <option value="cancelled" {{ old('status', $request?->status) == 'cancelled' ? 'selected' : '' }}>{{ __('messages.Cancelled') }}</option>
            </select>
        </div>
        <div class="col-md-6">
            <label class="form-label">{{ __('messages.Assigned to') }}</label>
            <select name="assigned_to" class="form-select">
                <option value="">{{ __('messages.Not assigned') }}</option>
                {{-- Only Housekeeping role users are listed --}}
                @foreach($users ?? [] as $u)
                    <option value="{{ $u->id }}" {{ old('assigned_to', $request?->assigned_to) == $u->id ? 'selected' : '' }}>{{ $u->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-12">
            <label class="form-label">{{ __('messages.Resolution notes') }}</label>
            <textarea name="resolution_notes" class="form-control" rows="3" placeholder="{{ __('messages.Resolution notes') }}">{{ old('resolution_notes', $request?->resolution_notes) }}</textarea>
        </div>
        <div class="col-12">
            <button type="submit" class="btn btn-primary">{{ $request ? __('messages.Update') : __('messages.Create') }}</button>
            @if($request)
                <a href="{{ route('maintenance.show', $request) }}" class="btn btn-secondary">{{ __('messages.Cancel') }}</a>
            @else
                <a href="{{ route('maintenance.index') }}" class="btn btn-secondary">{{ __('messages.Cancel') }}</a>
            @endif
        </div>
    </div>
</form>
@endsection
