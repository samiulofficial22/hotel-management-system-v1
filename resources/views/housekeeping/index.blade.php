@extends('layouts.app')
@section('title', 'Housekeeping')
@section('content')
<div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
    <div>
        <h1 class="h3 mb-2">Housekeeping</h1>
        <p class="mb-0 text-muted small">
            {{ __('Welcome,') }} <strong>{{ auth()->user()->name }}</strong>
            <span
                class="badge bg-secondary ms-1">{{ auth()->user()->roles->pluck('name')->join(', ') ?: __('No role') }}</span>
        </p>
    </div>
    @can('housekeeping.assign_others')
        <a href="{{ route('housekeeping.all-work') }}" class="btn btn-primary btn-sm">
            <i class="bi bi-table me-1"></i>{{ __('All Housekeeping Work') }}
        </a>
    @endcan
</div>

{{-- Date + Room filter --}}
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body py-3">
        <form method="GET" action="{{ route('housekeeping.index') }}" class="d-flex flex-wrap align-items-center gap-3">
            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('housekeeping.index', ['date' => $date->copy()->subDay()->format('Y-m-d'), 'room_id' => $roomId]) }}"
                    class="btn btn-outline-secondary btn-sm" title="Previous day">‹</a>
                <label class="mb-0 fw-medium">{{ $date->format('l, F j, Y') }}</label>
                <a href="{{ route('housekeeping.index', ['date' => $date->copy()->addDay()->format('Y-m-d'), 'room_id' => $roomId]) }}"
                    class="btn btn-outline-secondary btn-sm" title="Next day">›</a>
            </div>
            <div class="d-flex align-items-center gap-2">
                <input type="date" name="date" class="form-control form-control-sm" style="width: auto;"
                    value="{{ $date->format('Y-m-d') }}">
                <select name="room_id" class="form-select form-select-sm" style="width: auto;">
                    <option value="">{{ __('All rooms') }}</option>
                    @foreach($rooms as $r)
                        <option value="{{ $r->id }}" {{ (isset($roomId) && $roomId == $r->id) ? 'selected' : '' }}>
                            {{ $r->number }} ({{ $r->roomType->name ?? '-' }})</option>
                    @endforeach
                </select>
                <button type="submit" class="btn btn-primary btn-sm">{{ __('Search') }}</button>
            </div>
            <a href="{{ route('housekeeping.index', ['date' => now()->format('Y-m-d')]) }}"
                class="btn btn-outline-secondary btn-sm">Today</a>
        </form>
    </div>
</div>

@can('housekeeping.assign_others')
    {{-- Admin/Manager only: Assign room to any housekeeper. Housekeepers cannot assign others. --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-light py-2">
            <strong>Assign Room</strong>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('housekeeping.assign') }}" class="row g-2 align-items-end">
                @csrf
                <input type="hidden" name="date" value="{{ $date->format('Y-m-d') }}">
                <div class="col-md-3">
                    <label class="form-label small text-muted mb-0">Room</label>
                    <select name="room_id" class="form-select form-select-sm @error('room_id') is-invalid @enderror"
                        required>
                        <option value="">— Select room —</option>
                        @foreach($unassignedRooms as $r)
                            <option value="{{ $r->id }}">{{ $r->number }} ({{ $r->roomType->name ?? '-' }})</option>
                        @endforeach
                    </select>
                    @error('room_id')
                        <div class="invalid-feedback small">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-3">
                    <label class="form-label small text-muted mb-0">Assign to</label>
                    <select name="assigned_to" class="form-select form-select-sm" required>
                        @foreach($users as $u)
                            <option value="{{ $u->id }}">{{ $u->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-success btn-sm w-100">Assign</button>
                </div>
            </form>
        </div>
    </div>
@else
{{-- Housekeeper: Assign room to myself only --}}
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-light py-2">
        <strong>{{ __('Assign room to myself') }}</strong>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('housekeeping.assign') }}" class="row g-2 align-items-end">
            @csrf
            <input type="hidden" name="date" value="{{ $date->format('Y-m-d') }}">
            <div class="col-md-4">
                <label class="form-label small text-muted mb-0">Room</label>
                <select name="room_id" class="form-select form-select-sm @error('room_id') is-invalid @enderror"
                    required>
                    <option value="">— {{ __('Select room') }} —</option>
                    @foreach($unassignedRooms as $r)
                        <option value="{{ $r->id }}">{{ $r->number }} ({{ $r->roomType->name ?? '-' }})</option>
                    @endforeach
                </select>
                @error('room_id')
                    <div class="invalid-feedback small">{{ $message }}</div>
                @enderror
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-success btn-sm w-100">Assign</button>
            </div>
        </form>
    </div>
</div>
@endif

@if($roomsNeedingAttention->isNotEmpty())
    <div class="card border-0 shadow-sm border-start border-4 border-warning mb-4">
        <div class="card-header bg-white py-2 d-flex justify-content-between align-items-center">
            <strong class="text-warning"><i
                    class="bi bi-exclamation-triangle-fill me-2"></i>{{ __('Rooms Needing Attention / Cleaning (Unassigned)') }}</strong>
            <span class="badge bg-warning text-dark">{{ $roomsNeedingAttention->count() }} {{ __('room(s)') }}</span>
        </div>
        <div class="table-responsive">
            <table class="table table-sm align-middle mb-0">
                <thead class="table-light small">
                    <tr>
                        <th class="ps-3">Room</th>
                        <th>Type</th>
                        <th>Status</th>
                        <th>Reference</th>
                        <th class="text-end pe-3">Quick Assign</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($roomsNeedingAttention as $room)
                        <tr>
                            <td class="fw-bold ps-3">{{ $room->number }}</td>
                            <td class="small">{{ $room->roomType->name }}</td>
                            <td>
                                @php $rCfg = \App\Models\Room::statusBadgeConfig($room->status); @endphp
                                <span class="badge {{ $rCfg['class'] }}">{{ $rCfg['label'] }}</span>
                            </td>
                            <td class="small text-muted">
                                @if($room->status === \App\Models\Room::STATUS_OCCUPIED)
                                    {{ $room->latestBooking?->guest?->name ?? 'Guest' }} ({{ __('Occupied') }})
                                @else
                                    {{ __('Needs Checkout Cleaning') }}
                                @endif
                            </td>
                            <td class="text-end pe-3">
                                <form method="POST" action="{{ route('housekeeping.assign') }}"
                                    class="d-flex justify-content-end gap-2">
                                    @csrf
                                    <input type="hidden" name="date" value="{{ $date->format('Y-m-d') }}">
                                    <input type="hidden" name="room_id" value="{{ $room->id }}">
                                    <select name="assigned_to" class="form-select form-select-sm w-auto" required
                                        @cannot('housekeeping.assign_others') disabled @endcannot>
                                        @can('housekeeping.assign_others')
                                            <option value="">— Housekeeper —</option>
                                            @foreach($users as $u)
                                                <option value="{{ $u->id }}">{{ $u->name }}</option>
                                            @endforeach
                                        @else
                                            <option value="{{ auth()->id() }}">{{ auth()->user()->name }}</option>
                                        @endcan
                                    </select>
                                    <button type="submit" class="btn btn-success btn-sm">{{ __('Assign') }}</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endif

{{-- Assignments list --}}
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white d-flex justify-content-between align-items-center py-2">
        <strong>Assignments for {{ $date->format('M j, Y') }}</strong>
        <span class="badge bg-primary">{{ $assignments->count() }} room(s)</span>
    </div>
    @if($assignments->isEmpty())
        <div class="card-body text-center text-muted py-5">
            <p class="mb-0">{{ __('No assignments for this date.') }}</p>
            @can('housekeeping.assign_others')
                <p class="small mb-0 mt-1">{{ __('Use the form above to assign a room to a staff member.') }}</p>
            @else
                <p class="small mb-0 mt-1">{{ __('Use the form above to assign a room to yourself.') }}</p>
            @endcan
        </div>
    @else
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Room</th>
                        <th>Type</th>
                        <th>Assigned to</th>
                        <th>Status</th>
                        <th>Assigned at</th>
                        <th>Completed at</th>
                        @can('housekeeping.assign_others')
                            <th>Completion notes</th>
                        @endcan
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($assignments as $a)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>
                                <span class="fw-medium">{{ $a->room->number ?? '-' }}</span>
                            </td>
                            <td class="text-muted small">{{ $a->room->roomType->name ?? '-' }}</td>
                            <td>{{ $a->assignedTo->name ?? '-' }}</td>
                            <td>
                                @php $cfg = \App\Models\HousekeepingAssignment::statusBadgeConfig($a->status); @endphp
                                <span class="badge {{ $cfg['class'] }}">{{ $cfg['label'] }}</span>
                            </td>
                            <td class="small text-nowrap">{{ $a->created_at?->format('d M Y, h:i A') ?? '-' }}</td>
                            <td class="small text-nowrap">{{ $a->completed_at?->format('d M Y, h:i A') ?? '-' }}</td>
                            @can('housekeeping.assign_others')
                                <td class="small">{{ $a->status === 'completed' && $a->notes ? $a->notes : '-' }}</td>
                            @endcan
                            <td class="text-end">
                                @can('housekeeping.assign_others')
                                    <a href="{{ route('housekeeping.edit', $a) }}"
                                        class="btn btn-sm btn-outline-primary me-1">{{ __('Edit') }}</a>
                                    <form action="{{ route('housekeeping.destroy', $a) }}" method="POST" class="d-inline"
                                        onsubmit="return confirm('{{ __('Are you sure you want to delete this assignment?') }}');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">{{ __('Delete') }}</button>
                                    </form>
                                    <span class="me-1"></span>
                                @endcan
                                @if($a->room->status === 'occupied' && $a->room->latestBooking)
                                    <button type="button" class="btn btn-sm btn-outline-info me-1" data-bs-toggle="modal"
                                        data-bs-target="#refreshmentModal{{ $a->id }}" title="{{ __('Record Refreshment') }}">
                                        <i class="bi bi-cup-straw"></i>
                                    </button>
                                    <div class="modal fade" id="refreshmentModal{{ $a->id }}" tabindex="-1">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content">
                                                <form action="{{ route('housekeeping.refreshment.record') }}" method="POST">
                                                    @csrf
                                                    <input type="hidden" name="booking_id"
                                                        value="{{ $a->room->latestBooking->id }}">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title text-dark">Record Refreshment – Room
                                                            {{ $a->room->number }}</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body text-start">
                                                        <div class="alert alert-info py-2 small mb-3">
                                                            <i class="bi bi-info-circle me-1"></i>
                                                            Charging to guest:
                                                            <strong>{{ $a->room->latestBooking->guest->name ?? 'Guest' }}</strong>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label small">Item</label>
                                                            <select name="item_id" class="form-select" required>
                                                                <option value="">— Select item —</option>
                                                                @foreach($refreshmentItems as $item)
                                                                    <option value="{{ $item->id }}">{{ $item->name }}
                                                                        ({{ number_format($item->price, 2) }})</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label small">Quantity</label>
                                                            <input type="number" name="quantity" class="form-control" value="1"
                                                                min="1" required>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary"
                                                            data-bs-dismiss="modal">Cancel</button>
                                                        <button type="submit" class="btn btn-info">Record Consumption</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                                @if($a->status === 'pending')
                                    @can('housekeeping.assign_others')
                                        <form action="{{ route('housekeeping.reassign', $a) }}" method="POST"
                                            class="d-inline-block me-1">
                                            @csrf
                                            <select name="assigned_to" class="form-select form-select-sm d-inline-block w-auto"
                                                onchange="this.form.submit()">
                                                @foreach($users as $u)
                                                    <option value="{{ $u->id }}" {{ $a->assigned_to == $u->id ? 'selected' : '' }}>
                                                        {{ $u->name }}</option>
                                                @endforeach
                                            </select>
                                        </form>
                                    @endcan
                                    <form action="{{ route('housekeeping.start', $a) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-info">Start</button>
                                    </form>
                                @endif
                                @if($a->status === 'in_progress')
                                    <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal"
                                        data-bs-target="#completeModal{{ $a->id }}">Complete</button>
                                    <div class="modal fade" id="completeModal{{ $a->id }}" tabindex="-1">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content">
                                                <form action="{{ route('housekeeping.complete', $a) }}" method="POST">
                                                    @csrf
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Complete – Room {{ $a->room->number ?? '' }}</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <label class="form-label small">Notes (optional)</label>
                                                        <textarea name="notes" class="form-control" rows="2"
                                                            placeholder="Any notes...">{{ $a->notes }}</textarea>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary"
                                                            data-bs-dismiss="modal">Cancel</button>
                                                        <button type="submit" class="btn btn-success">Mark Complete</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                                @if($a->status === 'completed')
                                    <span
                                        class="d-block small text-muted mb-1">{{ $a->notes ? \Illuminate\Support\Str::limit($a->notes, 40) : '-' }}</span>
                                    @php
                                        $canEditNotes = $a->assigned_to === auth()->id() || auth()->user()->can('housekeeping.assign_others');
                                    @endphp
                                    @if($canEditNotes)
                                        <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal"
                                            data-bs-target="#editNotesModal{{ $a->id }}">{{ __('Edit') }}</button>
                                        <div class="modal fade" id="editNotesModal{{ $a->id }}" tabindex="-1">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content">
                                                    <form action="{{ route('housekeeping.notes.update', $a) }}" method="POST">
                                                        @csrf
                                                        @method('PATCH')
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">{{ __('Edit completion notes') }} – Room
                                                                {{ $a->room->number ?? '' }}</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <label class="form-label small">{{ __('Message / Notes') }}</label>
                                                            <textarea name="notes" class="form-control" rows="3"
                                                                placeholder="{{ __('Completion notes...') }}">{{ $a->notes }}</textarea>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary"
                                                                data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                                                            <button type="submit" class="btn btn-primary">{{ __('Update') }}</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection