@extends('layouts.app')
@section('title', 'Item Sales Report')
@section('content')
<div class="mb-4">
    <h1 class="h3 mb-0">Item Sales Report (Refreshments)</h1>
</div>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form action="{{ route('refreshments.transactions.report') }}" method="GET" class="row g-3 align-items-end">
            <div class="col-md-4">
                <label class="form-label small">From Date</label>
                <input type="date" name="from" class="form-control" value="{{ $from->format('Y-m-d') }}">
            </div>
            <div class="col-md-4">
                <label class="form-label small">To Date</label>
                <input type="date" name="to" class="form-control" value="{{ $to->format('Y-m-d') }}">
            </div>
            <div class="col-md-4">
                <button type="submit" class="btn btn-primary w-100">Generate Report</button>
            </div>
        </form>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>Item Name</th>
                        <th class="text-center">Total Quantity Consumed</th>
                        <th class="text-end">Total Revenue</th>
                        <th class="text-end">Avg. Price</th>
                    </tr>
                </thead>
                <tbody>
                    @php $grandTotal = 0; @endphp
                    @forelse($itemStats as $stat)
                    @php $grandTotal += $stat->total_revenue; @endphp
                    <tr>
                        <td class="fw-bold">{{ $stat->item->name ?? 'Unknown' }}</td>
                        <td class="text-center">{{ $stat->total_quantity }}</td>
                        <td class="text-end">{{ money($stat->total_revenue) }}</td>
                        <td class="text-end">{{ money($stat->total_revenue / $stat->total_quantity) }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center py-4 text-muted">No data found for this range.</td>
                    </tr>
                    @endforelse
                </tbody>
                @if($itemStats->count() > 0)
                <tfoot class="bg-light">
                    <tr>
                        <th colspan="2" class="text-end">Grand Total</th>
                        <th class="text-end text-primary">{{ money($grandTotal) }}</th>
                        <th></th>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>
</div>
@endsection
