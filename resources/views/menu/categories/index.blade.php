@extends('layouts.app')
@section('title', 'Menu - ' . $outlet->name)
@section('content')
<h1 class="h3 mb-4">Menu: {{ $outlet->name }}</h1>
<p>
    <a href="{{ route('menu.categories.create', $outlet) }}" class="btn btn-primary">Add Category</a>
    <a href="{{ route('menu.items.index', $outlet) }}" class="btn btn-outline-primary">Menu Items</a>
    <a href="{{ route('pos.outlet', $outlet->id) }}" class="btn btn-outline-secondary">POS</a>
</p>
<table class="table table-striped">
<thead><tr><th>Name</th><th>Sort</th><th>Active</th><th></th></tr></thead>
<tbody>
@foreach($categories as $c)
<tr>
    <td>{{ $c->name }}</td>
    <td>{{ $c->sort_order }}</td>
    <td>{{ $c->is_active ? 'Yes' : 'No' }}</td>
    <td><a href="{{ route('menu.categories.edit', [$outlet, $c]) }}" class="btn btn-sm btn-outline-primary">Edit</a></td>
</tr>
@endforeach
</tbody>
</table>
@if($categories->isEmpty())
<p class="text-muted">No categories. Add a category to start building the menu.</p>
@endif
<a href="{{ route('outlets.index') }}" class="btn btn-outline-secondary mt-2">Back to Outlets</a>
@endsection
