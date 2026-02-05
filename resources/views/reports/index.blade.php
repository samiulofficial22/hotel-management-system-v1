@extends('layouts.app')
@section('title', 'Reports')
@section('content')
<h1 class="h3 mb-4">Reports</h1>
{{-- NEW – SAFE ADDITION: Optional department filter only when config enabled and departments exist --}}
@if(($departmentFilterEnabled ?? false) && ($departments ?? collect())->isNotEmpty())
<p class="text-muted small mb-2">{{ __('messages.Department') }} {{ __('messages.optional') }}: {{ __('messages.Filter by department in report pages when enabled in config.') }}</p>
@endif
<div class="list-group">
<a href="{{ route('reports.dashboard') }}" class="list-group-item list-group-item-action">Dashboard summary</a>
<a href="{{ route('reports.occupancy') }}" class="list-group-item list-group-item-action">Occupancy</a>
<a href="{{ route('reports.revenue') }}" class="list-group-item list-group-item-action">Revenue</a>
</div>
@endsection
