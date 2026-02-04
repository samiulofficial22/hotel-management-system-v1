@extends('layouts.app')
@section('title', 'Revenue Report')
@section('content')
<h1 class="h3 mb-4">Revenue Report</h1>
<p>Period: {{ $period === 'today' ? 'Today' : 'This month' }}</p>
<p>Revenue: {{ number_format($revenue ?? 0, 2) }}</p>
<a href="{{ route('reports.index') }}" class="btn btn-secondary">Back</a>
@endsection
