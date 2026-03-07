@extends('layouts.app')
@section('title', 'Edit Banquet Venue')
@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h1 class="h5 mb-0">Edit Banquet Venue: {{ $venue->name }}</h1>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('banquet.venues.update', $venue) }}">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Venue Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" value="{{ old('name', $venue->name) }}" required>
                            @error('name')<div class="text-danger small">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Location</label>
                            <input type="text" name="location" class="form-control" value="{{ old('location', $venue->location) }}" placeholder="e.g. 2nd Floor, West Wing">
                            @error('location')<div class="text-danger small">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">Capacity (Persons) <span class="text-danger">*</span></label>
                            <input type="number" name="capacity" class="form-control" min="1" value="{{ old('capacity', $venue->capacity) }}" required>
                            @error('capacity')<div class="text-danger small">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">Hourly Rate (৳)</label>
                            <input type="number" name="hourly_rate" class="form-control" step="0.01" min="0" value="{{ old('hourly_rate', $venue->hourly_rate) }}">
                            @error('hourly_rate')<div class="text-danger small">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">Fixed Rate (৳)</label>
                            <input type="number" name="fixed_rate" class="form-control" step="0.01" min="0" value="{{ old('fixed_rate', $venue->fixed_rate) }}">
                            @error('fixed_rate')<div class="text-danger small">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Description</label>
                        <textarea name="description" class="form-control" rows="3">{{ old('description', $venue->description) }}</textarea>
                        @error('description')<div class="text-danger small">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-4">
                        <div class="form-check form-switch">
                            <input type="checkbox" name="is_active" value="1" class="form-check-input" id="isActive" {{ $venue->is_active ? 'checked' : '' }}>
                            <label class="form-check-label fw-bold" for="isActive">Active and available for booking</label>
                        </div>
                    </div>

                    <hr>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('banquet.venues.index') }}" class="btn btn-light px-4 text-muted">Cancel</a>
                        <button type="submit" class="btn btn-primary px-4 shadow-sm">
                            <i class="fas fa-save me-1"></i> Update Venue
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
