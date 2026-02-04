@extends('layouts.app')
@section('title', 'Campaign')
@section('content')
<h1 class="h3 mb-4">Campaign: {{ $campaign->name }}</h1>
<p>Type: {{ $campaign->type }} | Start: {{ $campaign->start_date->format('Y-m-d') }} | Status: {{ $campaign->status }}</p>
<p>Recipients: {{ $campaign->recipients->count() }}</p>
@can('marketing.manage')
<form action="{{ route('marketing.recipients', $campaign) }}" method="POST" class="mb-3">@csrf<button type="submit" class="btn btn-primary">Add all guests as recipients</button></form>
@endcan
<a href="{{ route('marketing.edit', $campaign) }}" class="btn btn-outline-primary">Edit</a>
<a href="{{ route('marketing.index') }}" class="btn btn-secondary">Back</a>
@endsection
