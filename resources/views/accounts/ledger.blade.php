@extends('layouts.app')
@section('title', 'Ledger')
@section('content')
<h1 class="h3 mb-4">Ledger</h1>
<form method="GET" action="{{ route('accounts.ledger') }}" class="mb-3">
<select name="account_id" class="form-select d-inline-block w-auto"><option value="">All accounts</option>@foreach($accounts as $a)<option value="{{ $a->id }}" {{ (string)($accountId ?? '') === (string)$a->id ? 'selected' : '' }}>{{ $a->code }} {{ $a->name }}</option>@endforeach</select>
<button type="submit" class="btn btn-outline-primary">Filter</button>
</form>
@can('accounts.manage')
<form method="POST" action="{{ route('accounts.entry.store') }}" class="mb-3 row g-2 align-items-end">
@csrf
<input type="date" name="entry_date" value="{{ date('Y-m-d') }}" required>
<select name="account_id" required>@foreach($accounts as $a)<option value="{{ $a->id }}">{{ $a->code }} {{ $a->name }}</option>@endforeach</select>
<input type="number" name="debit" step="0.01" min="0" placeholder="Debit">
<input type="number" name="credit" step="0.01" min="0" placeholder="Credit">
<input type="text" name="description" placeholder="Description">
<button type="submit" class="btn btn-primary">Add Entry</button>
</form>
@endcan
<table class="table table-striped">
<thead><tr><th>Date</th><th>Account</th><th>Description</th><th>Debit</th><th>Credit</th></tr></thead>
<tbody>
@foreach($entries as $e)
<tr><td>{{ $e->entry_date->format('Y-m-d') }}</td><td>{{ $e->account->code ?? '-' }}</td><td>{{ $e->description ?? '-' }}</td><td>{{ number_format($e->debit, 2) }}</td><td>{{ number_format($e->credit, 2) }}</td></tr>
@endforeach
</tbody>
</table>
{{ $entries->links() }}
@endsection
