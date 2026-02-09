@extends('layouts.app')
@section('title', 'Daily Cash Summary')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Daily Cash Summary</h1>
    <a href="{{ route('accounts.reports.index') }}" class="btn btn-outline-secondary">Back to Reports</a>
</div>
<form method="GET" action="{{ route('accounts.reports.daily-cash') }}" class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <div class="row g-2 align-items-end">
            <div class="col-auto"><label class="form-label small">From</label><input type="date" name="from" class="form-control form-control-sm" value="{{ $from->format('Y-m-d') }}"></div>
            <div class="col-auto"><label class="form-label small">To</label><input type="date" name="to" class="form-control form-control-sm" value="{{ $to->format('Y-m-d') }}"></div>
            <div class="col-auto"><button type="submit" class="btn btn-primary btn-sm">Apply</button></div>
        </div>
    </div>
</form>
<div class="card border-0 shadow-sm">
    <div class="card-body">
        <table class="table table-striped mb-0">
            <thead><tr><th>Date</th><th class="text-end">Net (Dr - Cr)</th></tr></thead>
            <tbody>
                @foreach($daily as $date => $net)
                <tr><td>{{ $date }}</td><td class="text-end {{ $net >= 0 ? '' : 'text-danger' }}">{{ money($net) }}</td></tr>
                @endforeach
            </tbody>
        </table>
        @if(empty($daily))<p class="text-muted mb-0">No cash movement in this period or Cash account not set up.</p>@endif
    </div>
</div>
@endsection
