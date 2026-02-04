@extends('layouts.app')
@section('title', 'Edit Account')
@section('content')
<h1 class="h3 mb-4">Edit: {{ $account->name }}</h1>
<form method="POST" action="{{ route('accounts.update', $account) }}">
@csrf
@method('PUT')
<div class="mb-3"><label class="form-label">Code</label><input type="text" name="code" class="form-control" value="{{ old('code', $account->code) }}" required></div>
<div class="mb-3"><label class="form-label">Name</label><input type="text" name="name" class="form-control" value="{{ old('name', $account->name) }}" required></div>
<div class="mb-3"><label class="form-label">Type</label><select name="type" class="form-select" required><option value="asset" {{ ($account->type ?? '') == 'asset' ? 'selected' : '' }}>Asset</option><option value="liability" {{ ($account->type ?? '') == 'liability' ? 'selected' : '' }}>Liability</option><option value="equity" {{ ($account->type ?? '') == 'equity' ? 'selected' : '' }}>Equity</option><option value="revenue" {{ ($account->type ?? '') == 'revenue' ? 'selected' : '' }}>Revenue</option><option value="expense" {{ ($account->type ?? '') == 'expense' ? 'selected' : '' }}>Expense</option></select></div>
<button type="submit" class="btn btn-primary">Update</button>
<a href="{{ route('accounts.index') }}" class="btn btn-secondary">Cancel</a>
</form>
@endsection
