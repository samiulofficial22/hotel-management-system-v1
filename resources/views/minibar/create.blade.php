@extends('layouts.app')
@section('title', 'Add Minibar Item')
@section('content')
<h1 class="h3 mb-4">Add Minibar Item</h1>
<form method="POST" action="{{ route('minibar.store') }}">
@csrf
<div class="mb-3"><label class="form-label">Name</label><input type="text" name="name" class="form-control" required></div>
<div class="mb-3"><label class="form-label">Price</label><input type="number" name="price" class="form-control" step="0.01" min="0" required></div>
<div class="mb-3"><label class="form-label">Quantity in stock</label><input type="number" name="quantity_in_stock" class="form-control" min="0" value="0"></div>
<button type="submit" class="btn btn-primary">Create</button>
<a href="{{ route('minibar.index') }}" class="btn btn-secondary">Cancel</a>
</form>
@endsection
