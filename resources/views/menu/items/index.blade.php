@extends('layouts.app')
@section('title', 'Menu Items')
@section('content')
<h1 class="h3 mb-4">Menu Items: {{ $outlet->name }}</h1>
<p>
    <a href="{{ route('menu.items.create', $outlet) }}" class="btn btn-primary">Add Item</a>
    <a href="{{ route('menu.categories.index', $outlet) }}" class="btn btn-outline-secondary">Categories</a>
</p>
<table class="table table-striped">
<thead><tr><th>Name</th><th>Category</th><th>Price</th><th>Cost</th><th>Available</th><th></th></tr></thead>
<tbody>
@foreach($items as $item)
<tr>
    <td>{{ $item->name }}</td>
    <td>{{ $item->menuCategory->name ?? '-' }}</td>
    <td>{{ number_format($item->price, 2) }}</td>
    <td>{{ $item->cost !== null ? number_format($item->cost, 2) : '–' }}</td>
    <td>{{ $item->is_available ? 'Yes' : 'No' }}</td>
    <td><a href="{{ route('menu.items.edit', [$outlet, $item]) }}" class="btn btn-sm btn-outline-primary">Edit</a></td>
</tr>
@endforeach
</tbody>
</table>
@if($items->isEmpty())
<p class="text-muted">No menu items. Add categories first, then add items.</p>
@endif
<a href="{{ route('outlets.index') }}" class="btn btn-outline-secondary mt-2">Back to Outlets</a>
@endsection
