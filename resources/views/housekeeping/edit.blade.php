@extends('layouts.app')
@section('title', __('Edit assignment'))
@section('content')
<div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
    <h1 class="h3 mb-0">{{ __('Edit assignment') }}</h1>
    <a href="{{ route('housekeeping.index', ['date' => $assignment->date->format('Y-m-d')]) }}" class="btn btn-outline-secondary btn-sm">{{ __('Back to list') }}</a>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <form method="POST" action="{{ route('housekeeping.update', $assignment) }}">
            @csrf
            @method('PUT')
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">{{ __('Room') }}</label>
                    <select name="room_id" class="form-select @error('room_id') is-invalid @enderror" required>
                        <option value="">— {{ __('Select room') }} —</option>
                        @foreach($rooms as $r)
                        <option value="{{ $r->id }}" {{ old('room_id', $assignment->room_id) == $r->id ? 'selected' : '' }}>{{ $r->number }} ({{ $r->roomType->name ?? '-' }})</option>
                        @endforeach
                    </select>
                    @error('room_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label">{{ __('Date') }}</label>
                    <input type="date" name="date" class="form-control @error('date') is-invalid @enderror" value="{{ old('date', $assignment->date->format('Y-m-d')) }}" required>
                    @error('date')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label">{{ __('Assign to') }}</label>
                    <select name="assigned_to" class="form-select @error('assigned_to') is-invalid @enderror" required>
                        @foreach($users as $u)
                        <option value="{{ $u->id }}" {{ old('assigned_to', $assignment->assigned_to) == $u->id ? 'selected' : '' }}>{{ $u->name }}</option>
                        @endforeach
                    </select>
                    @error('assigned_to')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label">{{ __('Status') }}</label>
                    <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                        @foreach(['pending' => __('Pending'), 'in_progress' => __('In Progress'), 'completed' => __('Completed')] as $value => $label)
                        <option value="{{ $value }}" {{ old('status', $assignment->status) === $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('status')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="mt-4 d-flex gap-2">
                <button type="submit" class="btn btn-primary">{{ __('Update') }}</button>
                <a href="{{ route('housekeeping.index', ['date' => $assignment->date->format('Y-m-d')]) }}" class="btn btn-outline-secondary">{{ __('Cancel') }}</a>
            </div>
        </form>
    </div>
</div>
@endsection
