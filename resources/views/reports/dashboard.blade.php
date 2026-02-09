@extends('layouts.app')
@section('title', 'Dashboard Report')
@section('content')
<h1 class="h3 mb-4">Dashboard Report</h1>
<div class="row mb-4">
<div class="col-md-4"><div class="card"><div class="card-body"><h5>Occupancy today</h5><p class="mb-0">{{ $occupancy['occupied'] ?? 0 }} / {{ $occupancy['total'] ?? 0 }} ({{ $occupancy['percentage'] ?? 0 }}%)</p></div></div></div>
<div class="col-md-4"><div class="card"><div class="card-body"><h5>Revenue today</h5><p class="mb-0">{{ money($revenueToday ?? 0) }}</p></div></div></div>
<div class="col-md-4"><div class="card"><div class="card-body"><h5>Revenue this month</h5><p class="mb-0">{{ money($revenueMonth ?? 0) }}</p></div></div></div>
</div>
<h5>Alerts</h5>
<ul><li>Check-outs today: {{ $alerts['check_outs_today']->count() ?? 0 }}</li><li>Check-ins today: {{ $alerts['check_ins_today']->count() ?? 0 }}</li><li>Rooms in cleaning: {{ $alerts['rooms_cleaning'] ?? 0 }}</li></ul>
<a href="{{ route('reports.index') }}" class="btn btn-secondary">Back to Reports</a>
@endsection
