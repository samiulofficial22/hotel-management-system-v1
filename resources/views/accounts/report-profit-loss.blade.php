@extends('layouts.app')
@section('title', 'Profit and Loss')
@section('content')
<h1 class="h3 mb-4">Profit and Loss</h1>
<form method="GET" action="{{ route('accounts.reports.profit-loss') }}" class="mb-4 row g-2 align-items-end">
    <div class="col-auto">
        <label class="form-label small mb-1">From</label>
        <input type="date" name="from" value="{{ $from->format('Y-m-d') }}" class="form-control form-control-sm">
    </div>
    <div class="col-auto">
        <label class="form-label small mb-1">To</label>
        <input type="date" name="to" value="{{ $to->format('Y-m-d') }}" class="form-control form-control-sm">
    </div>
    <div class="col-auto mt-3 mt-md-0">
        <button type="submit" class="btn btn-primary btn-sm">Apply</button>
    </div>
</form>

<div class="row g-3">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <h6 class="text-muted small text-uppercase mb-2">Summary</h6>
                <p class="mb-1">Total Revenue: {{ money($revenue) }}</p>
                <p class="mb-1">Total Expense: ({{ money($expense) }})</p>
                <p class="mb-0"><strong>Net Profit: {{ money($profit) }}</strong></p>
            </div>
        </div>
    </div>
    <div class="col-md-8">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body" style="min-height:260px;">
                <h6 class="text-muted small text-uppercase mb-2">Profit &amp; Loss Chart</h6>
                <canvas id="profitLossChart" style="max-height:220px;"></canvas>
            </div>
        </div>
    </div>
</div>

<a href="{{ route('accounts.reports.index') }}" class="btn btn-secondary mt-3">Back to Reports</a>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    (function () {
        const ctx = document.getElementById('profitLossChart');
        if (!ctx) return;
        if (typeof Chart === 'undefined') return;

        const data = {
            labels: ['Revenue', 'Expense', 'Net profit'],
            datasets: [{
                label: 'Amount (৳)',
                data: [{{ (float) $revenue }}, {{ (float) $expense }}, {{ (float) $profit }}],
                backgroundColor: [
                    'rgba(25, 135, 84, 0.7)',   // Revenue - green
                    'rgba(220, 53, 69, 0.7)',   // Expense - red
                    'rgba(13, 110, 253, 0.7)',  // Profit - blue
                ],
                borderColor: [
                    'rgba(25, 135, 84, 1)',
                    'rgba(220, 53, 69, 1)',
                    'rgba(13, 110, 253, 1)',
                ],
                borderWidth: 1,
                borderRadius: 6,
            }]
        };

        new Chart(ctx, {
            type: 'bar',
            data: data,
            options: {
                responsive: true,
                maintainAspectRatio: true,
                aspectRatio: 2, // width : height
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return '৳ ' + Number(value).toLocaleString();
                            }
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const v = context.parsed && typeof context.parsed.y !== 'undefined'
                                    ? context.parsed.y
                                    : 0;
                                return context.label + ': ৳ ' + Number(v).toLocaleString();
                            }
                        }
                    }
                }
            }
        });
    })();
</script>
@endpush
