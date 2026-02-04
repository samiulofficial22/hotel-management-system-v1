@extends('layouts.app')
@section('title', 'Occupancy Report')
@section('content')
<h1 class="h3 mb-4">Occupancy Report</h1>
<p>Occupancy: {{ $occupancy['occupied'] ?? 0 }} / {{ $occupancy['total'] ?? 0 }} rooms ({{ $occupancy['percentage'] ?? 0 }}%)</p>
<a href="{{ route('reports.index') }}" class="btn btn-secondary">Back</a>
@endsection
