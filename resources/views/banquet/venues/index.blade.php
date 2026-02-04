@extends('layouts.app')
@section('title', 'Banquet Venues')
@section('content')
<h1 class="h3 mb-4">Banquet Venues</h1>
<p>
    <a href="{{ route('banquet.venues.create') }}" class="btn btn-primary">Add Venue</a>
    <a href="{{ route('banquet.bookings.index') }}" class="btn btn-outline-primary">Bookings</a>
</p>
<table class="table table-striped">
<thead><tr><th>Name</th><th>Capacity</th><th>Active</th><th></th></tr></thead>
<tbody>
@foreach($venues as $v)
<tr>
    <td>{{ $v->name }}</td>
    <td>{{ $v->capacity }}</td>
    <td>{{ $v->is_active ? 'Yes' : 'No' }}</td>
    <td><a href="{{ route('banquet.venues.edit', $v) }}">Edit</a></td>
</tr>
@endforeach
</tbody>
</table>
@if($venues->isEmpty())
<p class="text-muted">No banquet venues yet.</p>
@endif
@endsection
