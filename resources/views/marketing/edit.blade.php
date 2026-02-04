@extends('layouts.app')
@section('title', 'Edit Campaign')
@section('content')
<h1 class="h3 mb-4">Edit: {{ $campaign->name }}</h1>
<form method="POST" action="{{ route('marketing.update', $campaign) }}">
@csrf
@method('PUT')
<div class="mb-3"><label class="form-label">Name</label><input type="text" name="name" class="form-control" value="{{ old('name', $campaign->name) }}" required></div>
<div class="mb-3"><label class="form-label">Start date</label><input type="date" name="start_date" class="form-control" value="{{ old('start_date', $campaign->start_date?->format('Y-m-d')) }}" required></div>
<div class="mb-3"><label class="form-label">Status</label><select name="status" class="form-select"><option value="draft">Draft</option><option value="active">Active</option><option value="completed">Completed</option></select></div>
<button type="submit" class="btn btn-primary">Update</button>
<a href="{{ route('marketing.show', $campaign) }}" class="btn btn-secondary">Cancel</a>
</form>
@endsection
