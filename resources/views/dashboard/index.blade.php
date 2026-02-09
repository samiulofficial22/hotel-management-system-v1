@extends('layouts.app')
@section('title', __('messages.Dashboard'))

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">{{ __('messages.Dashboard') }}</h1>

    <div class="d-flex flex-wrap gap-2">
        @can('bookings.manage')
            <a href="{{ route('bookings.create') }}" class="btn btn-primary btn-sm">{{ __('messages.New Booking') }}</a>
        @endcan
        @can('guests.manage')
            <a href="{{ route('guests.create') }}" class="btn btn-outline-primary btn-sm">{{ __('messages.Add Guest') }}</a>
        @endcan
        @can('pos.manage')
            <a href="{{ route('pos.index') }}" class="btn btn-outline-success btn-sm">{{ __('POS') }}</a>
        @endcan
        @can('hr.manage')
            <a href="{{ route('hr.employees.create') }}" class="btn btn-outline-secondary btn-sm">{{ __('messages.Add Employee') }}</a>
        @endcan
    </div>
</div>

{{-- TOP SUMMARY CARDS --}}
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <h6 class="text-muted text-uppercase small mb-1">{{ __('Rooms') }}</h6>
                <h3 class="mb-0">{{ $summary['total_rooms'] ?? 0 }}</h3>
                <small class="text-muted">{{ __('messages.Occupancy Today') }}: {{ $occupancy['percentage'] ?? 0 }}% ({{ $occupancy['occupied'] ?? 0 }}/{{ $occupancy['total'] ?? 0 }})</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <h6 class="text-muted text-uppercase small mb-1">{{ __('Available Rooms') }}</h6>
                <h3 class="mb-0 text-success">{{ $summary['available_rooms'] ?? 0 }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <h6 class="text-muted text-uppercase small mb-1">{{ __('Occupied Rooms') }}</h6>
                <h3 class="mb-0 text-primary">{{ $summary['occupied_rooms'] ?? 0 }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <h6 class="text-muted text-uppercase small mb-1">{{ __('Today Check-in / Check-out') }}</h6>
                <h3 class="mb-0">{{ $summary['today_checkins'] ?? 0 }} / {{ $summary['today_checkouts'] ?? 0 }}</h3>
                <small class="text-muted">{{ __('Pending requests') }}: {{ $summary['pending_bookings'] ?? 0 }}</small>
            </div>
        </div>
    </div>
</div>

{{-- FINANCIAL OVERVIEW + CHARTS --}}
@canany(['reports.view', 'accounts.view'])
<div class="row g-3 mb-4">
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <h6 class="text-muted text-uppercase small mb-2">{{ __('Financial Overview') }}</h6>
                <p class="mb-1">{{ __('Today Revenue') }}: {{ money($financial['today_revenue'] ?? 0) }}</p>
                <p class="mb-1">{{ __('This Month Revenue') }}: {{ money($financial['month_revenue'] ?? 0) }}</p>
                <p class="mb-1">{{ __('Outstanding Dues') }}: {{ money($financial['outstanding_dues'] ?? 0) }}</p>
                <p class="mb-1">{{ __('Today POS Sales') }}: {{ money($financial['today_pos_sales'] ?? 0) }}</p>
                <p class="mb-0">{{ __('Monthly Payroll Expense') }}: {{ money($financial['monthly_payroll'] ?? 0) }}</p>
            </div>
        </div>
    </div>
    <div class="col-lg-8">
        <div class="row g-3">
            <div class="col-md-7">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body" style="min-height:220px;">
                        <h6 class="text-muted small text-uppercase mb-2">{{ __('Revenue Trend (7 days)') }}</h6>
                        <canvas id="revenueTrendChart" style="max-height:200px;"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-5">
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-body" style="height:180px; overflow:hidden;">
                        <h6 class="text-muted small text-uppercase mb-2">{{ __('POS vs Room Revenue') }}</h6>
                        <canvas id="posRoomChart" style="max-height:150px; width:100%;"></canvas>
                    </div>
                </div>
                <div class="card border-0 shadow-sm">
                    <div class="card-body" style="height:180px; overflow:hidden;">
                        <h6 class="text-muted small text-uppercase mb-2">{{ __('Expense vs Income (month)') }}</h6>
                        <canvas id="expenseIncomeChart" style="max-height:150px; width:100%;"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endcanany

{{-- ROOM & BOOKING SNAPSHOT --}}
<div class="row g-3 mb-4">
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <h6 class="text-muted small text-uppercase mb-2">{{ __('Rooms by status') }}</h6>
                @php($r = $roomBooking['rooms_by_status'] ?? [])
                <ul class="list-unstyled mb-0 small">
                    <li>{{ __('Available') }}: <strong>{{ $r['available'] ?? 0 }}</strong></li>
                    <li>{{ __('Reserved') }}: <strong>{{ $r['reserved'] ?? 0 }}</strong></li>
                    <li>{{ __('Occupied') }}: <strong>{{ $r['occupied'] ?? 0 }}</strong></li>
                    <li>{{ __('Maintenance') }}: <strong>{{ $r['maintenance'] ?? 0 }}</strong></li>
                    <li>{{ __('Cleaning') }}: <strong>{{ $r['cleaning'] ?? 0 }}</strong></li>
                </ul>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white">{{ __('Latest bookings') }}</div>
            <div class="card-body p-0">
                @forelse(($roomBooking['latest_bookings'] ?? []) as $b)
                    <div class="d-flex justify-content-between align-items-center border-bottom p-3 small">
                        <div>
                            <div class="fw-semibold">{{ $b->guest->full_name ?? 'N/A' }}</div>
                            <div class="text-muted">{{ __('messages.Room') }} {{ $b->room->number ?? '-' }} · {{ $b->check_in_date->format('M d') }} - {{ $b->check_out_date->format('M d') }}</div>
                        </div>
                        <a href="{{ route('bookings.show', $b) }}" class="btn btn-sm btn-outline-primary">{{ __('messages.View') }}</a>
                    </div>
                @empty
                    <p class="p-3 mb-0 text-muted">{{ __('messages.No bookings yet.') }}</p>
                @endforelse
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white">{{ __('Pending booking approvals') }}</div>
            <div class="card-body p-0">
                @forelse(($roomBooking['pending_approvals'] ?? []) as $b)
                    <div class="d-flex justify-content-between align-items-center border-bottom p-3 small">
                        <div>
                            <div class="fw-semibold">{{ $b->guest->full_name ?? 'N/A' }}</div>
                            <div class="text-muted">{{ __('messages.Room') }} {{ $b->room->number ?? '-' }} · {{ $b->check_in_date->format('M d') }}</div>
                        </div>
                        <a href="{{ route('bookings.show', $b) }}" class="btn btn-sm btn-outline-primary">{{ __('messages.View') }}</a>
                    </div>
                @empty
                    <p class="p-3 mb-0 text-muted">{{ __('messages.No maintenance requests.') }}</p>
                @endforelse
            </div>
        </div>
    </div>
</div>

{{-- GUEST & STAFF OVERVIEW --}}
<div class="row g-3 mb-4">
    <div class="col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <h6 class="text-muted small text-uppercase mb-2">{{ __('Guests') }}</h6>
                <p class="mb-1">{{ __('Current staying guests') }}: <strong>{{ $guestStaff['current_staying_guests'] ?? 0 }}</strong></p>
                <p class="mb-0">{{ __('Pending guest approvals') }}: <strong>{{ $guestStaff['pending_guest_approvals'] ?? 0 }}</strong></p>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <h6 class="text-muted small text-uppercase mb-2">{{ __('Staff & Attendance') }}</h6>
                <p class="mb-1">{{ __('Total employees') }}: <strong>{{ $guestStaff['total_employees'] ?? 0 }}</strong></p>
                <p class="mb-1">{{ __('Present today') }}: <strong class="text-success">{{ $guestStaff['present_today'] ?? 0 }}</strong></p>
                <p class="mb-0">{{ __('Absent today (approx)') }}: <strong class="text-danger">{{ $guestStaff['absent_today'] ?? 0 }}</strong></p>
                <p class="mb-0 mt-2 small text-muted">{{ __('Payroll runs pending') }}: {{ $guestStaff['payroll_pending'] ?? 0 }}</p>
            </div>
        </div>
    </div>
</div>

{{-- POS SNAPSHOT --}}
@can('pos.view')
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <h6 class="text-muted small text-uppercase mb-2">{{ __('POS Snapshot') }}</h6>
                <p class="mb-1">{{ __('Today POS orders') }}: <strong>{{ $pos['today_orders'] ?? 0 }}</strong></p>
                <p class="mb-0">{{ __('Room charges pending to settle') }}: <strong>{{ money($pos['room_charges_pending'] ?? 0) }}</strong></p>
            </div>
        </div>
    </div>
    <div class="col-md-8">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body" style="min-height:200px;">
                <h6 class="text-muted small text-uppercase mb-2">{{ __('Today POS revenue by type') }}</h6>
                @php($rev = $pos['revenue_by_type'] ?? [])
                @if(empty($rev))
                    <p class="text-muted mb-0">{{ __('No POS orders today.') }}</p>
                @else
                    <ul class="list-unstyled mb-0 small">
                        @foreach($rev as $type => $amount)
                            <li>{{ str_replace('_', ' ', ucfirst($type ?? 'unknown')) }}: <strong>{{ money($amount) }}</strong></li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>
    </div>
</div>
@endcan

{{-- ALERTS & ACTION ITEMS --}}
<div class="row g-3 mb-4">
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white">{{ __('Unpaid invoices') }}</div>
            <div class="card-body p-0">
                @forelse(($extendedAlerts['unpaid_invoices'] ?? []) as $inv)
                    <div class="border-bottom p-3 small d-flex justify-content-between align-items-center">
                        <div>
                            <div class="fw-semibold">{{ $inv->invoice_number }}</div>
                            <div class="text-muted">{{ money($inv->balance_due) }}</div>
                        </div>
                        <a href="{{ route('bookings.show', $inv->booking_id) }}" class="btn btn-sm btn-outline-primary">{{ __('messages.View') }}</a>
                    </div>
                @empty
                    <p class="p-3 mb-0 text-muted">{{ __('No unpaid invoices.') }}</p>
                @endforelse
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white">{{ __('Low inventory items') }}</div>
            <div class="card-body p-0 small">
                <div class="p-2 border-bottom fw-semibold">{{ __('Minibar') }}</div>
                @forelse(($extendedAlerts['low_minibar_items'] ?? []) as $item)
                    <div class="d-flex justify-content-between align-items-center border-bottom px-3 py-2">
                        <span>{{ $item->name }}</span>
                        <span class="text-danger">{{ $item->quantity_in_stock }} {{ $item->unit }}</span>
                    </div>
                @empty
                    <p class="px-3 py-2 mb-0 text-muted">{{ __('No low minibar items.') }}</p>
                @endforelse
                <div class="p-2 border-bottom fw-semibold mt-2">{{ __('Store') }}</div>
                @forelse(($extendedAlerts['low_store_items'] ?? []) as $item)
                    <div class="d-flex justify-content-between align-items-center border-bottom px-3 py-2">
                        <span>{{ $item->name }}</span>
                        <span class="text-danger">{{ $item->quantity }} {{ $item->unit }}</span>
                    </div>
                @empty
                    <p class="px-3 py-2 mb-0 text-muted">{{ __('No low store items.') }}</p>
                @endforelse
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white">{{ __('Other alerts') }}</div>
            <div class="card-body small">
                <p class="mb-2">{{ __('Rooms in cleaning') }}: <strong>{{ $alerts['rooms_cleaning'] ?? 0 }}</strong></p>
                <p class="mb-2">{{ __('Pending check-outs today') }}: <strong>{{ count($alerts['check_outs_today'] ?? []) }}</strong></p>
                <p class="mb-2">{{ __('Pending check-ins today') }}: <strong>{{ count($alerts['check_ins_today'] ?? []) }}</strong></p>
                <p class="mb-0">{{ __('Unpaid payroll runs') }}: <strong>{{ count($extendedAlerts['unpaid_payroll_runs'] ?? []) }}</strong></p>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    (function () {
        if (typeof Chart === 'undefined') return;

        // Revenue trend chart
        const trendCtx = document.getElementById('revenueTrendChart');
        if (trendCtx) {
            const trendData = {
                labels: @json($charts['revenue_trend']['labels'] ?? []),
                datasets: [{
                    label: '{{ __('Revenue') }}',
                    data: @json($charts['revenue_trend']['values'] ?? []),
                    borderColor: 'rgba(13, 110, 253, 1)',
                    backgroundColor: 'rgba(13, 110, 253, 0.2)',
                    tension: 0.3,
                    fill: true,
                }]
            };
            new Chart(trendCtx, {
                type: 'line',
                data: trendData,
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    aspectRatio: 2, // width : height
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: value => '৳ ' + Number(value).toLocaleString()
                            }
                        }
                    },
                    plugins: {
                        legend: { display: false }
                    }
                }
            });
        }

        // POS vs Room chart
        const posRoomCtx = document.getElementById('posRoomChart');
        if (posRoomCtx) {
            const pos = {{ (float) ($charts['pos_vs_room']['pos'] ?? 0) }};
            const room = {{ (float) ($charts['pos_vs_room']['room'] ?? 0) }};
            new Chart(posRoomCtx, {
                type: 'doughnut',
                data: {
                    labels: ['POS', 'Room'],
                    datasets: [{
                        data: [pos, room],
                        backgroundColor: ['rgba(220,53,69,0.8)', 'rgba(25,135,84,0.8)'],
                        borderColor: ['rgba(220,53,69,1)', 'rgba(25,135,84,1)'],
                        borderWidth: 1,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'bottom' },
                        tooltip: {
                            callbacks: {
                                label: ctx => ctx.label + ': ৳ ' + Number(ctx.parsed).toLocaleString()
                            }
                        }
                    }
                }
            });
        }

        // Expense vs Income chart
        const expIncCtx = document.getElementById('expenseIncomeChart');
        if (expIncCtx) {
            const revenue = {{ (float) ($charts['expense_vs_income']['revenue'] ?? 0) }};
            const expense = {{ (float) ($charts['expense_vs_income']['expense'] ?? 0) }};
            new Chart(expIncCtx, {
                type: 'bar',
                data: {
                    labels: ['{{ __('Revenue') }}', '{{ __('Expense') }}'],
                    datasets: [{
                        data: [revenue, expense],
                        backgroundColor: ['rgba(13,110,253,0.8)', 'rgba(220,53,69,0.8)'],
                        borderColor: ['rgba(13,110,253,1)', 'rgba(220,53,69,1)'],
                        borderWidth: 1,
                        borderRadius: 6,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: value => '৳ ' + Number(value).toLocaleString()
                            }
                        }
                    },
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: ctx => ctx.label + ': ৳ ' + Number(ctx.parsed.y).toLocaleString()
                            }
                        }
                    }
                }
            });
        }
    })();
</script>
@endpush
@endsection
