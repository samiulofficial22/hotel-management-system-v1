@extends('layouts.app')
@section('title', 'Marketing Campaigns')
@section('content')
<h1 class="h3 mb-4">Marketing Campaigns</h1>
<p><a href="{{ route('marketing.create') }}" class="btn btn-primary">New Campaign</a></p>
<table class="table table-striped">
<thead><tr><th>Name</th><th>Type</th><th>Start</th><th>Status</th><th></th></tr></thead>
<tbody>
@foreach($campaigns as $c)
<tr><td>{{ $c->name }}</td><td>{{ $c->type }}</td><td>{{ $c->start_date->format('Y-m-d') }}</td><td>{{ $c->status }}</td><td><a href="{{ route('marketing.show', $c) }}">View</a> <a href="{{ route('marketing.edit', $c) }}">Edit</a></td></tr>
@endforeach
</tbody>
</table>
{{ $campaigns->links() }}
@endsection
