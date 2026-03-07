@extends('layouts.app')
@section('title', 'Room Refreshment Items')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Room Refreshment Items</h1>
    <a href="{{ route('refreshments.items.create') }}" class="btn btn-primary">Add Item</a>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>Item Name</th>
                        <th>Category</th>
                        <th>Price</th>
                        <th>Cost Price</th>
                        <th>Stock</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($items as $item)
                    <tr>
                        <td class="fw-bold">{{ $item->name }}</td>
                        <td>{{ $item->category }}</td>
                        <td>{{ money($item->price) }}</td>
                        <td>{{ money($item->cost_price) }}</td>
                        <td>
                            @if($item->stock_quantity <= 10)
                                <span class="badge bg-danger">Low: {{ $item->stock_quantity }}</span>
                            @else
                                <span class="badge bg-light text-dark border">{{ $item->stock_quantity }}</span>
                            @endif
                        </td>
                        <td>
                            @if($item->is_active)
                                <span class="badge bg-success">Active</span>
                            @else
                                <span class="badge bg-danger">Inactive</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <a href="{{ route('refreshments.items.edit', $item->id) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                            <form action="{{ route('refreshments.items.destroy', $item->id) }}" method="POST" class="d-inline">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete?')">Delete</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        {{ $items->links() }}
    </div>
</div>
@endsection
