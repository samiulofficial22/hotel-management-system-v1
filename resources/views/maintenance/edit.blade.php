@extends('layouts.app')
@section('title', __('messages.Edit') . ': ' . $request->title)
@section('content')
<h1 class="h3 mb-4">{{ __('messages.Edit') }}: {{ $request->title }}</h1>
<form method="POST" action="{{ route('maintenance.update', $request) }}">
@csrf
@method('PUT')
<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label">{{ __('messages.Status') }}</label>
        <select name="status" class="form-select">
            <option value="open" {{ old('status', $request->status) == 'open' ? 'selected' : '' }}>{{ __('messages.Open') }}</option>
            <option value="in_progress" {{ old('status', $request->status) == 'in_progress' ? 'selected' : '' }}>{{ __('messages.In Progress') }}</option>
            <option value="resolved" {{ old('status', $request->status) == 'resolved' ? 'selected' : '' }}>{{ __('messages.Resolved') }}</option>
            <option value="cancelled" {{ old('status', $request->status) == 'cancelled' ? 'selected' : '' }}>{{ __('messages.Cancelled') }}</option>
        </select>
    </div>
    <div class="col-md-6">
        <label class="form-label">{{ __('messages.Priority') }}</label>
        <select name="priority" class="form-select">
            <option value="low" {{ old('priority', $request->priority) == 'low' ? 'selected' : '' }}>{{ __('messages.Low') }}</option>
            <option value="normal" {{ old('priority', $request->priority) == 'normal' ? 'selected' : '' }}>{{ __('messages.Normal') }}</option>
            <option value="high" {{ old('priority', $request->priority) == 'high' ? 'selected' : '' }}>{{ __('messages.High') }}</option>
            <option value="urgent" {{ old('priority', $request->priority) == 'urgent' ? 'selected' : '' }}>{{ __('messages.Urgent') }}</option>
        </select>
    </div>
    <div class="col-md-6">
        <label class="form-label">{{ __('messages.Assigned to') }}</label>
        <select name="assigned_to" class="form-select">
            <option value="">{{ __('messages.Not assigned') }}</option>
            @foreach($users as $u)
            <option value="{{ $u->id }}" {{ old('assigned_to', $request->assigned_to) == $u->id ? 'selected' : '' }}>{{ $u->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-12">
        <label class="form-label">{{ __('messages.Resolution notes') }}</label>
        <textarea name="resolution_notes" class="form-control" rows="3" placeholder="{{ __('messages.Resolution notes') }}">{{ old('resolution_notes', $request->resolution_notes) }}</textarea>
    </div>
    <div class="col-12">
        <button type="submit" class="btn btn-primary">{{ __('messages.Update') }}</button>
        <a href="{{ route('maintenance.show', $request) }}" class="btn btn-secondary">{{ __('messages.Cancel') }}</a>
    </div>
</div>
</form>
@endsection
