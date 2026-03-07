@extends('layouts.app')
@section('title', 'Spa Services')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Spa Services</h1>
    <a href="{{ route('spa.services.create') }}" class="btn btn-primary">Add Service</a>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>Service Name</th>
                        <th>Duration</th>
                        <th>Price</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($services as $service)
                    <tr>
                        <td class="fw-bold">{{ $service->name }}</td>
                        <td>{{ $service->duration_minutes }} mins</td>
                        <td>{{ money($service->price) }}</td>
                        <td>
                            @if($service->is_active)
                                <span class="badge bg-success">Active</span>
                            @else
                                <span class="badge bg-danger">Inactive</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <a href="{{ route('spa.services.edit', $service) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                            <form action="{{ route('spa.services.destroy', $service) }}" method="POST" class="d-inline">
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
