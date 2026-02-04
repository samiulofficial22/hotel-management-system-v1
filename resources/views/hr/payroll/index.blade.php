@extends('layouts.app')
@section('title', 'Payroll')
@section('content')
<h1 class="h3 mb-4">Payroll</h1>
<p><a href="{{ route('hr.payroll.create') }}" class="btn btn-primary">New Payroll Run</a></p>
<table class="table table-striped">
<thead><tr><th>Title</th><th>Period</th><th>Status</th><th></th></tr></thead>
<tbody>
@foreach($runs as $r)
<tr><td>{{ $r->title ?? 'Payroll' }}</td><td>{{ $r->period_start->format('Y-m-d') }} - {{ $r->period_end->format('Y-m-d') }}</td><td>{{ $r->status }}</td><td><a href="{{ route('hr.payroll.show', $r) }}">View</a></td></tr>
@endforeach
</tbody>
</table>
{{ $runs->links() }}
@endsection
