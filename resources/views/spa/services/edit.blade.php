@extends('layouts.app')
@section('title', 'Edit Spa Service')
@section('content')
<div class="mb-4">
    <a href="{{ route('spa.services.index') }}" class="btn btn-link text-decoration-none p-0">
        <i class="bi bi-arrow-left"></i> Back to Services
    </a>
    <h1 class="h3 mb-0 mt-2">Edit: {{ $service->name }}</h1>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <form action="{{ route('spa.services.update', $service) }}" method="POST">
            @csrf @method('PUT')
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Service Name</label>
                    <input type="text" name="name" class="form-control" value="{{ $service->name }}" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Duration (Minutes)</label>
                    <input type="number" name="duration_minutes" class="form-control" value="{{ $service->duration_minutes }}" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Price (৳)</label>
                    <input type="number" step="0.01" name="price" class="form-control" value="{{ $service->price }}" required>
                </div>
                <div class="col-12">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-control" rows="3">{{ $service->description }}</textarea>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Active</label>
                    <select name="is_active" class="form-select">
                        <option value="1" {{ $service->is_active ? 'selected' : '' }}>Yes</option>
                        <option value="0" {{ !$service->is_active ? 'selected' : '' }}>No</option>
                    </select>
                </div>
                <div class="col-12 text-end">
                    <button type="submit" class="btn btn-primary">Update Service</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
