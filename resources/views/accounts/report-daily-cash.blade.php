@extends('layouts.app')
@section('title', 'Daily Cash Summary')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-1 font-weight-bold">Daily Cash Summary</h1>
        <p class="text-muted small mb-0">Track all cash-in and cash-out movements by date.</p>
    </div>
    <a href="{{ route('accounts.reports.index') }}" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-1"></i> Back to Reports
    </a>
</div>

<form method="GET" action="{{ route('accounts.reports.daily-cash') }}" class="card border-0 shadow-sm mb-4 bg-light">
    <div class="card-body py-3">
        <div class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label small fw-bold">From Date</label>
                <input type="date" name="from" class="form-control" value="{{ $from->format('Y-m-d') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-bold">To Date</label>
                <input type="date" name="to" class="form-control" value="{{ $to->format('Y-m-d') }}">
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-primary px-4">
                    <i class="fas fa-filter me-1"></i> Apply Filter
                </button>
            </div>
        </div>
    </div>
</form>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-primary text-white">
                    <tr>
                        <th class="ps-4">Date</th>
                        <th class="text-end">Opening Balance</th>
                        <th class="text-end">Cash In (Dr)</th>
                        <th class="text-end">Cash Out (Cr)</th>
                        <th class="text-end">Net Movement</th>
                        <th class="text-end pe-4">Closing Balance</th>
                    </tr>
                </thead>
                <tbody>
                    @php 
                        $totalIn = 0; 
                        $totalOut = 0; 
                    @endphp
                    @forelse($daily as $date => $data)
                        @php 
                            $totalIn += $data['cashIn']; 
                            $totalOut += $data['cashOut']; 
                        @endphp
                        <tr>
                            <td class="ps-4 fw-bold text-dark">{{ \Carbon\Carbon::parse($date)->format('d M, Y') }}</td>
                            <td class="text-end">{{ money($data['opening']) }}</td>
                            <td class="text-end text-success fw-bold">+ {{ money($data['cashIn']) }}</td>
                            <td class="text-end text-danger fw-bold">- {{ money($data['cashOut']) }}</td>
                            <td class="text-end {{ $data['net'] >= 0 ? 'text-primary' : 'text-danger' }}">
                                {{ $data['net'] >= 0 ? '+' : '' }}{{ money($data['net']) }}
                            </td>
                            <td class="text-end pe-4 fw-bold h6 mb-0">{{ money($data['closing']) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <i class="fas fa-search mb-3 opacity-25 fa-3x"></i>
                                <p class="text-muted mb-0">No cash movement found for the selected period.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                @if(!empty($daily))
                <tfoot class="bg-light">
                    <tr>
                        <th class="ps-4">PERIOD TOTALS</th>
                        <th></th>
                        <th class="text-end text-success fw-bold">+ {{ money($totalIn) }}</th>
                        <th class="text-end text-danger fw-bold">- {{ money($totalOut) }}</th>
                        <th class="text-end {{ ($totalIn - $totalOut) >= 0 ? 'text-primary' : 'text-danger' }}">
                            {{ ($totalIn - $totalOut) >= 0 ? '+' : '' }}{{ money($totalIn - $totalOut) }}
                        </th>
                        <th class="pe-4"></th>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>
</div>
@endsection
