@extends('layouts.app')
@section('title', 'Laundry Items')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Laundry Items</h1>
    <a href="{{ route('laundry.items.create') }}" class="btn btn-primary">Add Item</a>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>Item Name</th>
                        <th>Wash Price</th>
                        <th>Iron Price</th>
                        <th>Dry Clean Price</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($items as $item)
                    <tr>
                        <td class="fw-bold">{{ $item->name }}</td>
                        <td>{{ money($item->wash_price) }}</td>
                        <td>{{ money($item->iron_price) }}</td>
                        <td>{{ money($item->dry_clean_price) }}</td>
                        <td>
                            @if($item->is_active)
                                <span class="badge bg-success">Active</span>
                            @else
                                <span class="badge bg-danger">Inactive</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <a href="{{ route('laundry.items.edit', $item) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                            <form action="{{ route('laundry.items.destroy', $item) }}" method="POST" class="d-inline">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete?')">Delete</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
