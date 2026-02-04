@extends('layouts.app')
@section('title', 'Room Types')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Room Types</h1>
    <a href="{{ route('room-types.create') }}" class="btn btn-primary">Add Room Type</a>
</div>
<div class="table-responsive">
    <table class="table table-striped">
        <thead><tr><th>Name</th><th>Slug</th><th>Base Rate</th><th>Max Occupancy</th><th>Active</th><th></th></tr></thead>
        <tbody>
            @foreach($roomTypes as $rt)
            <tr>
                <td>{{ $rt->name }}</td>
                <td>{{ $rt->slug }}</td>
                <td>{{ number_format($rt->base_rate, 2) }}</td>
                <td>{{ $rt->max_occupancy }}</td>
                <td>{{ $rt->is_active ? 'Yes' : 'No' }}</td>
                <td>
                    <a href="{{ route('room-types.edit', $rt) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
                    <form action="{{ route('room-types.destroy', $rt) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete?');">@csrf @method('DELETE')<button type="submit" class="btn btn-sm btn-outline-danger">Delete</button></form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
{{ $roomTypes->links() }}
@endsection
