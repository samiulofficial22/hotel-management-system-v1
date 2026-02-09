@extends('layouts.app')
@section('title', 'Payroll Run')
@section('content')
<div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
    <h1 class="h3 mb-0">Payroll Run</h1>
    <a href="{{ route('hr.payroll.index') }}" class="btn btn-outline-secondary">Back</a>
</div>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <p class="mb-1"><strong>Period:</strong> {{ $run->period_start->format('Y-m-d') }} to {{ $run->period_end->format('Y-m-d') }}</p>
        <p class="mb-1"><strong>Title:</strong> {{ $run->title ?? '-' }}</p>
        <p class="mb-0">
            <strong>Status:</strong>
            @if($run->status === 'draft')<span class="badge bg-secondary">Draft</span>
            @elseif($run->status === 'processed')<span class="badge bg-warning text-dark">Approved</span>
            @else<span class="badge bg-success">Paid</span>
            @endif
            @if($run->paid_at)
                | Paid on {{ $run->paid_at->format('Y-m-d H:i') }} @if($run->paidBy) by {{ $run->paidBy->name }} @endif
            @endif
        </p>
    </div>
</div>

@can('payroll.approve')
@if($run->isDraft())
<form action="{{ route('hr.payroll.process', $run) }}" method="POST" class="mb-3">
    @csrf
    <button type="submit" class="btn btn-warning">Approve Payroll</button>
</form>
@endif
@endcan

@can('payroll.pay')
@if($run->status === 'processed' && $paymentAccounts->isNotEmpty())
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white">Mark as Paid</div>
    <div class="card-body">
        <form action="{{ route('hr.payroll.pay', $run) }}" method="POST" class="row g-2 align-items-end">
            @csrf
            <div class="col-auto">
                <label class="form-label small">Payment from account</label>
                <select name="payment_account_id" class="form-select" required>
                    @foreach($paymentAccounts as $acc)
                    <option value="{{ $acc->id }}">{{ $acc->code }} — {{ $acc->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-success">Mark as Paid</button>
            </div>
        </form>
        <p class="small text-muted mb-0 mt-2">Creates ledger entry: Salary Expense (Dr), Cash/Bank (Cr).</p>
    </div>
</div>
@endif
@endcan

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white">Salary items</div>
    <div class="table-responsive">
        <table class="table table-striped align-middle mb-0">
            <thead>
                <tr>
                    <th>Employee</th>
                    <th class="text-end">Base</th>
                    <th class="text-end">Overtime</th>
                    <th class="text-end">Allowances</th>
                    <th class="text-end">Deductions</th>
                    <th class="text-end">Net</th>
                    <th class="text-end">Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($run->items as $item)
                <tr>
                    <td>{{ $item->employee->full_name ?? '-' }}</td>
                    <td class="text-end">{{ money($item->base_salary) }}</td>
                    <td class="text-end">{{ money($item->overtime_amount ?? 0) }}</td>
                    <td class="text-end">{{ money($item->allowances) }}</td>
                    <td class="text-end">{{ money($item->deductions) }}</td>
                    <td class="text-end fw-bold">{{ money($item->net_salary) }}</td>
                    <td class="text-end">
                        @if($run->isPaid())
                        <a href="{{ route('hr.payroll.slip', $item) }}" class="btn btn-sm btn-outline-primary" target="_blank">Slip PDF</a>
                        @elseif(auth()->user()?->can('payroll.manage'))
                        <form action="{{ route('hr.payroll.item.update', $item) }}" method="POST" class="d-inline">
                            @csrf
                            @method('PUT')
                            <input type="number" step="0.01" name="allowances" value="{{ $item->allowances }}" class="form-control form-control-sm d-inline-block" style="width:80px" placeholder="Allow">
                            <input type="number" step="0.01" name="deductions" value="{{ $item->deductions }}" class="form-control form-control-sm d-inline-block" style="width:80px" placeholder="Ded">
                            <button type="submit" class="btn btn-sm btn-primary">Update</button>
                        </form>
                        @else
                        —
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="card-footer bg-white text-end">
        <strong>Total Net:</strong> {{ money($run->items->sum('net_salary')) }}
    </div>
</div>
@endsection
