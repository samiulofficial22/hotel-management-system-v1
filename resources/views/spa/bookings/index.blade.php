@extends('layouts.app')
@section('title', 'Spa Bookings')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Spa Bookings</h1>
    <a href="{{ route('spa.bookings.create') }}" class="btn btn-primary">New Booking</a>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>Booking #</th>
                        <th>Room</th>
                        <th>Service</th>
                        <th>Date & Time</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($bookings as $booking)
                    <tr>
                        <td class="fw-bold">#{{ $booking->id }}</td>
                        <td>{{ $booking->room->number ?? 'N/A' }}</td>
                        <td>{{ $booking->service->name }}</td>
                        <td>{{ $booking->booking_date->format('d M, Y') }} at {{ date('h:i A', strtotime($booking->booking_time)) }}</td>
                        <td>{{ money($booking->amount) }}</td>
                        <td>
                            @php
                                $statusClass = match($booking->status) {
                                    'pending' => 'bg-warning',
                                    'confirmed' => 'bg-info',
                                    'completed' => 'bg-success',
                                    'cancelled' => 'bg-danger',
                                    default => 'bg-secondary'
                                };
                            @endphp
                            <span class="badge {{ $statusClass }}">{{ ucfirst($booking->status) }}</span>
                        </td>
                        <td class="text-end">
                            <a href="{{ route('spa.bookings.show', $booking) }}" class="btn btn-sm btn-outline-info">View</a>
                            <form action="{{ route('spa.bookings.destroy', $booking) }}" method="POST" class="d-inline">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete?')">Delete</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        {{ $bookings->links() }}
    </div>
</div>
@endsection
