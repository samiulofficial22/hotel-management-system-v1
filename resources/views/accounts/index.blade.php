@extends('layouts.app')
@section('title', 'Chart of Accounts')
@section('content')
<h1 class="h3 mb-4">Chart of Accounts</h1>
<p>
    <a href="{{ route('accounts.create') }}" class="btn btn-primary">Add Account</a>
    <a href="{{ route('accounts.ledger') }}" class="btn btn-outline-primary">Ledger</a>
    <a href="{{ route('accounts.reports.index') }}" class="btn btn-outline-secondary">Reports</a>
</p>
<table class="table table-striped">
<thead><tr><th>ID</th><th>Code</th><th>Name</th><th>Type</th><th>Active</th><th></th></tr></thead>
<tbody>
@foreach($accounts as $a)
<tr><td>{{ $loop->iteration }}</td><td>{{ $a->code }}</td><td>{{ $a->name }}</td><td>{{ $a->type }}</td><td>{{ $a->is_active ? 'Yes' : 'No' }}</td><td><a href="{{ route('accounts.edit', $a) }}">Edit</a></td></tr>
@endforeach
</tbody>
</table>
@if($accounts->isEmpty())
<p class="text-muted">No accounts yet.</p>
@endif
@endsection
