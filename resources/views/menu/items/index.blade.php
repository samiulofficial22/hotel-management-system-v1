@extends('layouts.app')
@section('title', 'Menu Items')
@section('content')
<h1 class="h3 mb-4">Menu Items: {{ $outlet->name }}</h1>
<p>
    <a href="{{ route('menu.items.create', $outlet) }}" class="btn btn-primary">Add Item</a>
    <a href="{{ route('menu.categories.index', $outlet) }}" class="btn btn-outline-secondary">Categories</a>
</p>
<table class="table table-striped">
<thead><tr><th style="width:60px">Image</th><th>Name</th><th>Category</th><th>Price</th><th>Cost</th><th>Available</th><th></th></tr></thead>
<tbody>
@foreach($items as $item)
<tr>
    <td>
        <img src="{{ $item->image_url }}" alt="{{ $item->name }}" class="rounded" width="40" height="40" style="object-fit: cover;">
    </td>
    <td>{{ $item->name }}</td>
    <td>{{ $item->menuCategory->name ?? '-' }}</td>
    <td>{{ money($item->price) }}</td>
    <td>{{ $item->cost !== null ? money($item->cost) : '–' }}</td>
    <td>{{ $item->is_available ? 'Yes' : 'No' }}</td>
    <td class="text-nowrap">
        <a href="{{ route('menu.items.edit', [$outlet, $item]) }}" class="btn btn-sm btn-outline-primary">Edit</a>
        <form action="{{ route('menu.items.destroy', [$outlet, $item]) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('Delete this menu item?') }}');">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
        </form>
    </td>
</tr>
@endforeach
</tbody>
</table>
@if($items->isEmpty())
<p class="text-muted">No menu items. Add categories first, then add items.</p>
@endif
<a href="{{ route('outlets.index') }}" class="btn btn-outline-secondary mt-2">Back to Outlets</a>
@endsection
