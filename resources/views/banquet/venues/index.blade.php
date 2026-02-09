@extends('layouts.app')
@section('title', 'Banquet Venues')
@section('content')
<h1 class="h3 mb-4">Banquet Venues</h1>
<p>
    <a href="{{ route('banquet.venues.create') }}" class="btn btn-primary">Add Venue</a>
    <a href="{{ route('banquet.bookings.index') }}" class="btn btn-outline-primary">Bookings</a>
</p>
<table class="table table-striped">
<thead><tr><th>ID</th><th>Name</th><th>Capacity</th><th>Active</th><th></th></tr></thead>
<tbody>
@foreach($venues as $v)
<tr>
    <td>{{ $loop->iteration }}</td>
    <td>{{ $v->name }}</td>
    <td>{{ $v->capacity }}</td>
    <td>{{ $v->is_active ? 'Yes' : 'No' }}</td>
    <td class="text-nowrap">
        <a href="{{ route('banquet.venues.edit', $v) }}" class="btn btn-sm btn-outline-primary">{{ __('messages.Edit') }}</a>
        <form action="{{ route('banquet.venues.destroy', $v) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('Delete this venue?') }}');">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-sm btn-outline-danger">{{ __('messages.Delete') }}</button>
        </form>
    </td>
</tr>
@endforeach
</tbody>
</table>
@if($venues->isEmpty())
<p class="text-muted">No banquet venues yet.</p>
@endif
@endsection
