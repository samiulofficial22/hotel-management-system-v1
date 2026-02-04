@extends('layouts.app')
@section('title', 'Banquet Booking')
@section('content')
<h1 class="h3 mb-4">Banquet Booking: {{ $booking->event_name }}</h1>
<dl class="row">
    <dt class="col-sm-3">Venue</dt>
    <dd class="col-sm-9">{{ $booking->banquetVenue->name ?? '-' }}</dd>
    <dt class="col-sm-3">Event date</dt>
    <dd class="col-sm-9">{{ $booking->event_date->format('Y-m-d') }}</dd>
    <dt class="col-sm-3">Time</dt>
    <dd class="col-sm-9">{{ $booking->start_time }} - {{ $booking->end_time }}</dd>
    <dt class="col-sm-3">Guest count</dt>
    <dd class="col-sm-9">{{ $booking->guest_count }}</dd>
    <dt class="col-sm-3">Status</dt>
    <dd class="col-sm-9">{{ $booking->status }}</dd>
    <dt class="col-sm-3">Contact</dt>
    <dd class="col-sm-9">{{ $booking->contact_name }}, {{ $booking->contact_phone }}</dd>
</dl>
<a href="{{ route('banquet.bookings.edit', $booking) }}" class="btn btn-primary">Edit</a>
<a href="{{ route('banquet.bookings.index') }}" class="btn btn-secondary">Back</a>
@endsection
