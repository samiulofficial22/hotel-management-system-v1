@extends('layouts.app')
@section('title', 'Outlets')
@section('content')
<h1 class="h3 mb-4">Outlets (F&B)</h1>
<p><a href="{{ route('outlets.create') }}" class="btn btn-primary">Add Outlet</a> <a href="{{ route('pos.index') }}" class="btn btn-success">POS</a></p>
<table class="table table-striped">
<thead><tr><th>ID</th><th>Name</th><th>Type</th><th>Code</th><th>Active</th><th></th></tr></thead>
<tbody>
@foreach($outlets as $o)
<tr>
<td>{{ $loop->iteration }}</td>
<td>{{ $o->name }}</td>
<td>{{ $o->type }}</td>
<td>{{ $o->code ?? '-' }}</td>
<td>{{ $o->is_active ? 'Yes' : 'No' }}</td>
<td>
    <a href="{{ route('pos.outlet', $o->id) }}" class="btn btn-sm btn-outline-success">POS</a>
    <a href="{{ route('menu.categories.index', $o) }}" class="btn btn-sm btn-outline-secondary">Menu</a>
    <a href="{{ route('outlets.edit', $o) }}" class="btn btn-sm btn-outline-primary">Edit</a>
    <form action="{{ route('outlets.destroy', $o) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('Delete this outlet?') }}');">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
    </form>
</td>
</tr>
@endforeach
</tbody>
</table>
@endsection
