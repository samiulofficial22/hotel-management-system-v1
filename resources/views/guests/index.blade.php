@extends('layouts.app')
@section('title', 'Guests')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0">Guests</h1>
        <p class="text-muted small mb-0">Total: <strong>{{ $guests->total() }}</strong> {{ $guests->total() === 1 ? 'guest' : 'guests' }}</p>
    </div>
    <a href="{{ route('guests.create') }}" class="btn btn-primary">Add Guest</a>
</div>

<form action="{{ route('guests.index') }}" method="GET" class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <div class="row g-2 align-items-end">
            <div class="col-md-4">
                <label class="form-label small text-muted">Search (name, email, phone)</label>
                <input type="text" name="q" class="form-control form-control-sm" placeholder="Search guests..." value="{{ request('q') }}">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary btn-sm w-100">Search</button>
            </div>
            @if(request()->filled('q'))
            <div class="col-md-1">
                <a href="{{ route('guests.index') }}" class="btn btn-outline-secondary btn-sm w-100">Clear</a>
            </div>
            @endif
        </div>
    </div>
</form>

<div class="table-responsive">
<table class="table table-striped">
<thead><tr><th>Name</th><th>Email</th><th>Phone</th><th></th></tr></thead>
<tbody>
@foreach($guests as $g)
<tr>
<td>{{ $g->full_name }}</td>
<td>{{ $g->email ?? '-' }}</td>
<td>{{ $g->phone ?? '-' }}</td>
<td><a href="{{ route('guests.show', $g) }}" class="btn btn-sm btn-outline-primary">View</a> <a href="{{ route('guests.edit', $g) }}" class="btn btn-sm btn-outline-secondary">Edit</a></td>
</tr>
@endforeach
</tbody>
</table>
</div>
{{ $guests->links() }}
@endsection
