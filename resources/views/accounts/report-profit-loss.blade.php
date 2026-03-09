@extends('layouts.app')
@section('title', 'Profit and Loss')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-1 fw-bold">📈 Profit & Loss Report</h1>
        <p class="text-muted small mb-0">Period: <strong>{{ $from->format('d M Y') }}</strong> — <strong>{{ $to->format('d M Y') }}</strong></p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('accounts.reports.profit-loss.pdf-preview', ['from' => $from->format('Y-m-d'), 'to' => $to->format('Y-m-d')]) }}"
           class="btn btn-outline-primary btn-sm" target="_blank">
            <i class="fas fa-print me-1"></i> Preview PDF
        </a>
        <a href="{{ route('accounts.reports.profit-loss.pdf', ['from' => $from->format('Y-m-d'), 'to' => $to->format('Y-m-d')]) }}"
           class="btn btn-danger btn-sm">
            <i class="fas fa-file-pdf me-1"></i> Download PDF
        </a>
        <a href="{{ route('accounts.reports.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-arrow-left me-1"></i> Back to Reports
        </a>
    </div>
</div>
<form method="GET" action="{{ route('accounts.reports.profit-loss') }}" class="card border-0 shadow-sm mb-4 bg-light">
    <div class="card-body py-3">
        <div class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label small fw-bold">From Date</label>
                <input type="date" name="from" value="{{ $from->format('Y-m-d') }}" class="form-control">
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-bold">To Date</label>
                <input type="date" name="to" value="{{ $to->format('Y-m-d') }}" class="form-control">
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-primary px-4">
                    <i class="fas fa-filter me-1"></i> Apply Filter
                </button>
            </div>
        </div>
    </div>
</form>

<div class="row g-3">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <h6 class="text-muted small text-uppercase mb-2">Summary</h6>
                <div class="d-flex justify-content-between mb-1">
                    <span>Total Revenue</span>
                    <span class="text-success">{{ money($revenue) }}</span>
                </div>
                <div class="d-flex justify-content-between mb-1">
                    <span>Total Expense</span>
                    <span class="text-danger">({{ money($expense) }})</span>
                </div>
                <hr>
                <div class="d-flex justify-content-between">
                    <span class="fw-bold">Net Profit</span>
                    <span class="fw-bold {{ $profit >= 0 ? 'text-primary' : 'text-danger' }}">{{ money($profit) }}</span>
                </div>
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

<div class="row mt-4 g-3">
    <div class="col-md-6">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0 fw-bold text-success"><i class="fas fa-arrow-trend-up me-2"></i>Revenue Breakdown</h6>
            </div>
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-3">Account</th>
                            <th class="text-end pe-3">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($revenueBreakdown as $rb)
                        <tr>
                            <td class="ps-3">{{ $rb['name'] }}</td>
                            <td class="text-end pe-3 fw-bold">{{ money($rb['amount']) }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="2" class="text-center py-3 text-muted">No revenue recorded</td>
                        </tr>
                        @endforelse
                    </tbody>
                    <tfoot class="bg-light">
                        <tr>
                            <td class="ps-3 fw-bold">Total Revenue</td>
                            <td class="text-end pe-3 fw-bold text-success">{{ money($revenue) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0 fw-bold text-danger"><i class="fas fa-arrow-trend-down me-2"></i>Expense Breakdown</h6>
            </div>
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-3">Account</th>
                            <th class="text-end pe-3">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($expenseBreakdown as $eb)
                        <tr>
                            <td class="ps-3">{{ $eb['name'] }}</td>
                            <td class="text-end pe-3 fw-bold text-danger">({{ money($eb['amount']) }})</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="2" class="text-center py-3 text-muted">No expenses recorded</td>
                        </tr>
                        @endforelse
                    </tbody>
                    <tfoot class="bg-light">
                        <tr>
                            <td class="ps-3 fw-bold">Total Expense</td>
                            <td class="text-end pe-3 fw-bold text-danger">({{ money($expense) }})</td>
                        </tr>
                    </tfoot>
                </table>
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
