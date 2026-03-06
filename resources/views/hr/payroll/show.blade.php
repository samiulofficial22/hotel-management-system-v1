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
<form action="{{ route('hr.payroll.process', $run) }}" method="POST" class="d-inline mb-3">
    @csrf
    <button type="submit" class="btn btn-warning">Approve Payroll</button>
</form>
@elseif($run->status === 'processed')
<form action="{{ route('hr.payroll.revert', $run) }}" method="POST" class="d-inline mb-3">
    @csrf
    <button type="submit" class="btn btn-outline-warning" onclick="return confirm('Are you sure you want to revert this to draft? This allows editing again.')">Revert to Draft</button>
</form>
@endif
@endcan

@can('payroll.manage')
@if($run->status !== 'paid')
<form action="{{ route('hr.payroll.destroy', $run) }}" method="POST" class="d-inline mb-3 ms-2">
    @csrf
    @method('DELETE')
    <button type="submit" class="btn btn-outline-danger" onclick="return confirm('Are you sure you want to completely delete this payroll run? This cannot be undone.')">Delete Payroll Form</button>
</form>
@endif
@endcan

<div class="mb-3"></div>

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
                    <th class="text-end">Work Days</th>
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
                    <td>
                        {{ $item->employee->full_name ?? '-' }}
                        <div class="small text-muted">{{ $item->employee->departmentRelation->name ?? $item->employee->department ?? 'N/A' }}</div>
                    </td>
                    <td class="text-end">{{ money($item->base_salary) }}</td>
                    <td class="text-end">{{ number_format($item->working_days ?? 0, 1) }}</td>
                    <td class="text-end">{{ money($item->overtime_amount ?? 0) }}</td>
                    <td class="text-end">{{ money($item->allowances) }}</td>
                    <td class="text-end">{{ money($item->deductions) }}</td>
                    <td class="text-end fw-bold">{{ money($item->net_salary) }}</td>
                    <td class="text-end">
                        @if($run->isPaid())
                        <a href="{{ route('hr.payroll.slip', $item) }}" class="btn btn-sm btn-outline-primary" target="_blank">Slip PDF</a>
                        @elseif(auth()->user()?->can('payroll.manage'))
                        <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editItemModal{{ $item->id }}">
                            Edit
                        </button>

                        <!-- Edit Modal -->
                        <div class="modal fade" id="editItemModal{{ $item->id }}" tabindex="-1" aria-hidden="true" style="text-align: left;">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <form action="{{ route('hr.payroll.item.update', $item) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <div class="modal-header py-2">
                                            <h5 class="modal-title fs-6">Edit Payroll: {{ $item->employee->full_name ?? '-' }}</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body pb-0">
                                            <div class="row g-3 mb-3">
                                                <div class="col-6">
                                                    <label class="form-label small text-muted mb-1">Base Salary</label>
                                                    <input type="number" step="0.01" name="base_salary" value="{{ $item->base_salary }}" class="form-control form-control-sm" required>
                                                </div>
                                                <div class="col-6">
                                                    <label class="form-label small text-muted mb-1">Working Days</label>
                                                    <input type="number" step="0.5" name="working_days" value="{{ $item->working_days }}" class="form-control form-control-sm">
                                                </div>
                                                <div class="col-12">
                                                    <label class="form-label small text-muted mb-1">Overtime Amount</label>
                                                    <input type="number" step="0.01" name="overtime_amount" value="{{ $item->overtime_amount ?? 0 }}" class="form-control form-control-sm">
                                                </div>
                                                <div class="col-6">
                                                    <label class="form-label small text-muted mb-1">Bonus / Allowances (+)</label>
                                                    <input type="number" step="0.01" name="allowances" value="{{ $item->allowances }}" class="form-control form-control-sm" placeholder="Add Money">
                                                </div>
                                                <div class="col-6">
                                                    <label class="form-label small text-muted mb-1">Deductions (-)</label>
                                                    <input type="number" step="0.01" name="deductions" value="{{ $item->deductions }}" class="form-control form-control-sm" placeholder="Minus Money">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer py-2">
                                            <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                            <button type="submit" class="btn btn-sm btn-primary">Save Changes</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
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
