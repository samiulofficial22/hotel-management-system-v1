@extends('layouts.app')
@section('title', 'Accounts Reports')
@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-1 fw-bold">📊 Accounts Reports</h1>
        <p class="text-muted small mb-0">Generate, view and download financial reports as PDF</p>
    </div>
    <a href="{{ route('accounts.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="fas fa-book me-1"></i> Chart of Accounts
    </a>
</div>

<div class="row g-4">
    {{-- Profit & Loss --}}
    <div class="col-md-6">
        <div class="card border-0 shadow-sm h-100" style="border-top: 4px solid #3b82f6 !important;">
            <div class="card-body p-4">
                <div class="d-flex align-items-center mb-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center me-3 flex-shrink-0"
                         style="width:48px;height:48px;background:linear-gradient(135deg,#3b82f6,#2563eb);">
                        <i class="fas fa-chart-line text-white"></i>
                    </div>
                    <div>
                        <h5 class="mb-0 fw-bold">Profit & Loss</h5>
                        <small class="text-muted">Revenue vs Expense breakdown by date range</small>
                    </div>
                </div>
                <form method="GET" action="{{ route('accounts.reports.profit-loss') }}" class="mb-3">
                    <div class="row g-2">
                        <div class="col-6">
                            <label class="form-label small fw-semibold mb-1">From</label>
                            <input type="date" name="from" class="form-control form-control-sm" value="{{ now()->startOfMonth()->format('Y-m-d') }}">
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-semibold mb-1">To</label>
                            <input type="date" name="to" class="form-control form-control-sm" value="{{ now()->format('Y-m-d') }}">
                        </div>
                        <div class="col-12 d-flex gap-2 mt-1">
                            <button type="submit" class="btn btn-primary btn-sm flex-fill">
                                <i class="fas fa-eye me-1"></i> View
                            </button>
                            <button type="submit" form="pl-pdf-preview-form" class="btn btn-outline-secondary btn-sm flex-fill">
                                <i class="fas fa-print me-1"></i> Preview PDF
                            </button>
                            <button type="submit" form="pl-pdf-form" class="btn btn-outline-danger btn-sm flex-fill">
                                <i class="fas fa-file-pdf me-1"></i> Download
                            </button>
                        </div>
                    </div>
                </form>
                <form id="pl-pdf-form" method="GET" action="{{ route('accounts.reports.profit-loss.pdf') }}">
                    <input type="hidden" name="from">
                    <input type="hidden" name="to">
                </form>
                <form id="pl-pdf-preview-form" method="GET" action="{{ route('accounts.reports.profit-loss.pdf-preview') }}" target="_blank">
                    <input type="hidden" name="from">
                    <input type="hidden" name="to">
                </form>
            </div>
        </div>
    </div>

    {{-- Expense --}}
    <div class="col-md-6">
        <div class="card border-0 shadow-sm h-100" style="border-top: 4px solid #7c3aed !important;">
            <div class="card-body p-4">
                <div class="d-flex align-items-center mb-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center me-3 flex-shrink-0"
                         style="width:48px;height:48px;background:linear-gradient(135deg,#7c3aed,#5b21b6);">
                        <i class="fas fa-receipt text-white"></i>
                    </div>
                    <div>
                        <h5 class="mb-0 fw-bold">Expense Report</h5>
                        <small class="text-muted">Expense breakdown by account for a date range</small>
                    </div>
                </div>
                <form method="GET" action="{{ route('accounts.reports.expense') }}" class="mb-3" id="exp-view-form">
                    <div class="row g-2">
                        <div class="col-6">
                            <label class="form-label small fw-semibold mb-1">From</label>
                            <input type="date" name="from" class="form-control form-control-sm" value="{{ now()->startOfMonth()->format('Y-m-d') }}">
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-semibold mb-1">To</label>
                            <input type="date" name="to" class="form-control form-control-sm" value="{{ now()->format('Y-m-d') }}">
                        </div>
                        <div class="col-12 d-flex gap-2 mt-1">
                            <button type="submit" class="btn btn-sm flex-fill" style="background:#7c3aed;color:#fff;">
                                <i class="fas fa-eye me-1"></i> View
                            </button>
                            <button type="submit" form="exp-pdf-preview-form" class="btn btn-outline-secondary btn-sm flex-fill">
                                <i class="fas fa-print me-1"></i> Preview PDF
                            </button>
                            <button type="submit" form="exp-pdf-form" class="btn btn-outline-danger btn-sm flex-fill">
                                <i class="fas fa-file-pdf me-1"></i> Download
                            </button>
                        </div>
                    </div>
                </form>
                <form id="exp-pdf-form" method="GET" action="{{ route('accounts.reports.expense.pdf') }}">
                    <input type="hidden" name="from">
                    <input type="hidden" name="to">
                </form>
                <form id="exp-pdf-preview-form" method="GET" action="{{ route('accounts.reports.expense.pdf-preview') }}" target="_blank">
                    <input type="hidden" name="from">
                    <input type="hidden" name="to">
                </form>
            </div>
        </div>
    </div>

    {{-- Daily Cash --}}
    <div class="col-md-6">
        <div class="card border-0 shadow-sm h-100" style="border-top: 4px solid #059669 !important;">
            <div class="card-body p-4">
                <div class="d-flex align-items-center mb-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center me-3 flex-shrink-0"
                         style="width:48px;height:48px;background:linear-gradient(135deg,#059669,#065f46);">
                        <i class="fas fa-money-bill-wave text-white"></i>
                    </div>
                    <div>
                        <h5 class="mb-0 fw-bold">Daily Cash Summary</h5>
                        <small class="text-muted">Track daily cash-in and cash-out movements</small>
                    </div>
                </div>
                <form method="GET" action="{{ route('accounts.reports.daily-cash') }}" class="mb-3" id="cash-view-form">
                    <div class="row g-2">
                        <div class="col-6">
                            <label class="form-label small fw-semibold mb-1">From</label>
                            <input type="date" name="from" class="form-control form-control-sm" value="{{ now()->startOfMonth()->format('Y-m-d') }}">
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-semibold mb-1">To</label>
                            <input type="date" name="to" class="form-control form-control-sm" value="{{ now()->format('Y-m-d') }}">
                        </div>
                        <div class="col-12 d-flex gap-2 mt-1">
                            <button type="submit" class="btn btn-sm flex-fill" style="background:#059669;color:#fff;">
                                <i class="fas fa-eye me-1"></i> View
                            </button>
                            <button type="submit" form="cash-pdf-preview-form" class="btn btn-outline-secondary btn-sm flex-fill">
                                <i class="fas fa-print me-1"></i> Preview PDF
                            </button>
                            <button type="submit" form="cash-pdf-form" class="btn btn-outline-danger btn-sm flex-fill">
                                <i class="fas fa-file-pdf me-1"></i> Download
                            </button>
                        </div>
                    </div>
                </form>
                <form id="cash-pdf-form" method="GET" action="{{ route('accounts.reports.daily-cash.pdf') }}">
                    <input type="hidden" name="from">
                    <input type="hidden" name="to">
                </form>
                <form id="cash-pdf-preview-form" method="GET" action="{{ route('accounts.reports.daily-cash.pdf-preview') }}" target="_blank">
                    <input type="hidden" name="from">
                    <input type="hidden" name="to">
                </form>
            </div>
        </div>
    </div>

    {{-- Payroll Cost --}}
    <div class="col-md-6">
        <div class="card border-0 shadow-sm h-100" style="border-top: 4px solid #0284c7 !important;">
            <div class="card-body p-4">
                <div class="d-flex align-items-center mb-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center me-3 flex-shrink-0"
                         style="width:48px;height:48px;background:linear-gradient(135deg,#0284c7,#1e3a5f);">
                        <i class="fas fa-users text-white"></i>
                    </div>
                    <div>
                        <h5 class="mb-0 fw-bold">Payroll Cost Report</h5>
                        <small class="text-muted">Payroll runs paid within selected date range</small>
                    </div>
                </div>
                <form method="GET" action="{{ route('accounts.reports.payroll-cost') }}" class="mb-3" id="pay-view-form">
                    <div class="row g-2">
                        <div class="col-6">
                            <label class="form-label small fw-semibold mb-1">From</label>
                            <input type="date" name="from" class="form-control form-control-sm" value="{{ now()->startOfMonth()->format('Y-m-d') }}">
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-semibold mb-1">To</label>
                            <input type="date" name="to" class="form-control form-control-sm" value="{{ now()->format('Y-m-d') }}">
                        </div>
                        <div class="col-12 d-flex gap-2 mt-1">
                            <button type="submit" class="btn btn-sm flex-fill" style="background:#0284c7;color:#fff;">
                                <i class="fas fa-eye me-1"></i> View
                            </button>
                            <button type="submit" form="pay-pdf-preview-form" class="btn btn-outline-secondary btn-sm flex-fill">
                                <i class="fas fa-print me-1"></i> Preview PDF
                            </button>
                            <button type="submit" form="pay-pdf-form" class="btn btn-outline-danger btn-sm flex-fill">
                                <i class="fas fa-file-pdf me-1"></i> Download
                            </button>
                        </div>
                    </div>
                </form>
                <form id="pay-pdf-form" method="GET" action="{{ route('accounts.reports.payroll-cost.pdf') }}">
                    <input type="hidden" name="from">
                    <input type="hidden" name="to">
                </form>
                <form id="pay-pdf-preview-form" method="GET" action="{{ route('accounts.reports.payroll-cost.pdf-preview') }}" target="_blank">
                    <input type="hidden" name="from">
                    <input type="hidden" name="to">
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
// Sync date inputs from a view-form to pdf-forms on submit
document.addEventListener('DOMContentLoaded', function () {
    const pairForms = [
        { view: 'form[action="{{ route('accounts.reports.profit-loss') }}"]',  pdf: ['#pl-pdf-form', '#pl-pdf-preview-form'] },
        { view: '#exp-view-form',  pdf: ['#exp-pdf-form', '#exp-pdf-preview-form'] },
        { view: '#cash-view-form', pdf: ['#cash-pdf-form', '#cash-pdf-preview-form'] },
        { view: '#pay-view-form',  pdf: ['#pay-pdf-form', '#pay-pdf-preview-form'] },
    ];

    pairForms.forEach(function (pair) {
        const viewForm = document.querySelector(pair.view);
        if (!viewForm) return;

        pair.pdf.forEach(function(pdfSelector) {
            const pdfForm = document.querySelector(pdfSelector);
            if (!pdfForm) return;

            pdfForm.addEventListener('submit', function (e) {
                const fromInput = viewForm.querySelector('[name="from"]');
                const toInput   = viewForm.querySelector('[name="to"]');
                if (fromInput) pdfForm.querySelector('[name="from"]').value = fromInput.value;
                if (toInput)   pdfForm.querySelector('[name="to"]').value   = toInput.value;
            });
        });
    });
});
</script>
@endpush
