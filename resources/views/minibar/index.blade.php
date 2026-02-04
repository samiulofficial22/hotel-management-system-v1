@extends('layouts.app')
@section('title', 'Minibar')
@section('content')
<h1 class="h3 mb-4">Minibar Inventory</h1>
<p><a href="{{ route('minibar.create') }}" class="btn btn-primary">Add Item</a></p>
<table class="table table-striped">
<thead><tr><th>Name</th><th>Price</th><th>Qty</th><th></th></tr></thead>
<tbody>
@foreach($items as $i)
<tr><td>{{ $i->name }}</td><td>{{ number_format($i->price, 2) }}</td><td>{{ $i->quantity_in_stock }}</td><td><a href="{{ route('minibar.edit', $i) }}">Edit</a></td></tr>
@endforeach
</tbody>
</table>
@if($items->isEmpty())
<p class="text-muted">No items yet.</p>
@endif
@endsection
