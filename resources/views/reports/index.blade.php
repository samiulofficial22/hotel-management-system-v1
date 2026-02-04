@extends('layouts.app')
@section('title', 'Reports')
@section('content')
<h1 class="h3 mb-4">Reports</h1>
<div class="list-group">
<a href="{{ route('reports.dashboard') }}" class="list-group-item list-group-item-action">Dashboard summary</a>
<a href="{{ route('reports.occupancy') }}" class="list-group-item list-group-item-action">Occupancy</a>
<a href="{{ route('reports.revenue') }}" class="list-group-item list-group-item-action">Revenue</a>
</div>
@endsection
