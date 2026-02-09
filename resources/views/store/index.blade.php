@extends('layouts.app')
@section('title', 'Store')
@section('content')
<h1 class="h3 mb-4">Store Inventory</h1>
<p><a href="{{ route('store.create') }}" class="btn btn-primary">Add Item</a></p>
<table class="table table-striped">
<thead><tr><th>ID</th><th>Name</th><th>Qty</th><th>Unit</th><th></th></tr></thead>
<tbody>
@foreach($items as $i)
<tr><td>{{ $loop->iteration }}</td><td>{{ $i->name }}</td><td>{{ $i->quantity }}</td><td>{{ $i->unit }}</td><td><a href="{{ route('store.edit', $i) }}">Edit</a></td></tr>
@endforeach
</tbody>
</table>
@if($items->isEmpty())
<p class="text-muted">No items yet.</p>
@endif
@endsection
