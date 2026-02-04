@extends('layouts.app')
@section('title', 'Add Account')
@section('content')
<h1 class="h3 mb-4">Add Account</h1>
<form method="POST" action="{{ route('accounts.store') }}">
@csrf
<div class="mb-3"><label class="form-label">Code</label><input type="text" name="code" class="form-control" required></div>
<div class="mb-3"><label class="form-label">Name</label><input type="text" name="name" class="form-control" required></div>
<div class="mb-3"><label class="form-label">Type</label><select name="type" class="form-select" required><option value="asset">Asset</option><option value="liability">Liability</option><option value="equity">Equity</option><option value="revenue">Revenue</option><option value="expense">Expense</option></select></div>
<button type="submit" class="btn btn-primary">Create</button>
<a href="{{ route('accounts.index') }}" class="btn btn-secondary">Cancel</a>
</form>
@endsection
