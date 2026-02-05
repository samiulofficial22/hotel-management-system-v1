@extends('layouts.app')
@section('title', 'Edit Store Item')
@section('content')
<h1 class="h3 mb-4">Edit: {{ $item->name }}</h1>
<form method="POST" action="{{ route('store.update', $item) }}">
@csrf
@method('PUT')
<div class="mb-3">
    <label class="form-label">Name</label>
    <input type="text" name="name" class="form-control" value="{{ old('name', $item->name) }}" required>
</div>
<div class="mb-3">
    <label class="form-label">{{ __('messages.Department') }} ({{ __('messages.optional') }})</label>
    <select name="department_id" class="form-select">
        <option value="">{{ __('messages.Not assigned') }}</option>
        @foreach($departments ?? [] as $d)
        <option value="{{ $d->id }}" {{ old('department_id', $item->department_id) == $d->id ? 'selected' : '' }}>{{ $d->name }} ({{ $d->code }})</option>
        @endforeach
    </select>
</div>
<div class="mb-3">
    <label class="form-label">Quantity</label>
    <input type="number" name="quantity" class="form-control" min="0" value="{{ old('quantity', $item->quantity) }}">
</div>
<div class="mb-3 form-check">
    <input type="checkbox" name="is_active" value="1" class="form-check-input" {{ old('is_active', $item->is_active) ? 'checked' : '' }}>
    <label class="form-check-label">Active</label>
</div>
<button type="submit" class="btn btn-primary">Update</button>
<a href="{{ route('store.index') }}" class="btn btn-secondary">Cancel</a>
</form>
@endsection
