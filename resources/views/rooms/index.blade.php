@extends('layouts.app')
@section('title', 'Rooms')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0">Rooms</h1>
        <p class="text-muted small mb-0">Total: <strong>{{ $rooms->total() }}</strong> {{ $rooms->total() === 1 ? 'room' : 'rooms' }}</p>
    </div>
    <a href="{{ route('rooms.create') }}" class="btn btn-primary">Add Room</a>
</div>

<form action="{{ route('rooms.index') }}" method="GET" class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <div class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label small text-muted">Search (number / floor)</label>
                <input type="text" name="q" class="form-control form-control-sm" placeholder="Room no. or floor..." value="{{ request('q') }}">
            </div>
            <div class="col-md-2">
                <label class="form-label small text-muted">Room Type</label>
                <select name="room_type_id" class="form-select form-select-sm">
                    <option value="">All</option>
                    @foreach($roomTypes as $rt)
                    <option value="{{ $rt->id }}" {{ request('room_type_id') == $rt->id ? 'selected' : '' }}>{{ $rt->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small text-muted">Status</label>
                <select name="status" class="form-select form-select-sm">
                    <option value="">All</option>
                    @foreach([\App\Models\Room::STATUS_AVAILABLE, \App\Models\Room::STATUS_OCCUPIED, \App\Models\Room::STATUS_CLEANING, \App\Models\Room::STATUS_MAINTENANCE, \App\Models\Room::STATUS_OUT_OF_ORDER] as $s)
                    @php $cfg = \App\Models\Room::statusBadgeConfig($s); @endphp
                    <option value="{{ $s }}" {{ request('status') == $s ? 'selected' : '' }}>{{ $cfg['label'] }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary btn-sm w-100">Search</button>
            </div>
            @if(request()->hasAny(['q', 'room_type_id', 'status']))
            <div class="col-md-1">
                <a href="{{ route('rooms.index') }}" class="btn btn-outline-secondary btn-sm w-100">Clear</a>
            </div>
            @endif
        </div>
    </div>
</form>

<div class="table-responsive">
    <table class="table table-striped">
        <thead><tr><th>ID</th><th>Number</th><th>Type</th><th>Floor</th><th>Status</th><th></th></tr></thead>
        <tbody>
            @foreach($rooms as $r)
            <tr>
                <td>{{ ($rooms->currentPage() - 1) * $rooms->perPage() + $loop->iteration }}</td>
                <td>{{ $r->number }}</td>
                <td>{{ $r->roomType->name ?? '-' }}</td>
                <td>{{ $r->floor ?? '-' }}</td>
                <td>
                    @php $cfg = \App\Models\Room::statusBadgeConfig($r->status); @endphp
                    <span class="badge {{ $cfg['class'] }}">{{ $cfg['label'] }}</span>
                </td>
                <td>
                    <a href="{{ route('rooms.edit', $r) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
                    <form action="{{ route('rooms.destroy', $r) }}" method="POST" class="d-inline">@csrf @method('DELETE')<button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete?');">Delete</button></form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
{{ $rooms->links() }}
@endsection
