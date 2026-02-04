@extends('layouts.app')
@section('title', 'Add Store Item')
@section('content')
<h1 class="h3 mb-4">Add Store Item</h1>
<form method="POST" action="{{ route('store.store') }}">
@csrf
<div class="mb-3"><label class="form-label">Name</label><input type="text" name="name" class="form-control" required></div>
<div class="mb-3"><label class="form-label">Quantity</label><input type="number" name="quantity" class="form-control" min="0" value="0"></div>
<button type="submit" class="btn btn-primary">Create</button>
<a href="{{ route('store.index') }}" class="btn btn-secondary">Cancel</a>
</form>
@endsection
