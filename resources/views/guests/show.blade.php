@extends('layouts.app')
@section('title', 'Guest')
@section('content')
<h1 class="h3 mb-4">{{ $guest->full_name }}</h1>
<p>Email: {{ $guest->email ?? '-' }}</p>
<p>Phone: {{ $guest->phone ?? '-' }}</p>
@if($guest->address)<p>Address: {{ $guest->address }}</p>@endif
@if($guest->nid_number)<p>NID Number: {{ $guest->nid_number }}</p>@endif
@if($guest->nid_photo_front || $guest->nid_photo_back)
<p class="mb-2">NID Photos:</p>
<div class="row g-3 mb-3">
    @if($guest->nid_photo_front)
    <div class="col-md-6">
        <div class="card border shadow-sm">
            <div class="card-header small bg-light py-1">NID – Front</div>
            <div class="card-body p-2 text-center">
                <a href="{{ asset('storage/' . $guest->nid_photo_front) }}" target="_blank" class="d-inline-block">
                    <img src="{{ asset('storage/' . $guest->nid_photo_front) }}" alt="NID Front" class="img-fluid rounded" style="max-height: 280px; object-fit: contain;">
                </a>
                <small class="d-block mt-1"><a href="{{ asset('storage/' . $guest->nid_photo_front) }}" target="_blank">Open full size</a></small>
            </div>
        </div>
    </div>
    @endif
    @if($guest->nid_photo_back)
    <div class="col-md-6">
        <div class="card border shadow-sm">
            <div class="card-header small bg-light py-1">NID – Back</div>
            <div class="card-body p-2 text-center">
                <a href="{{ asset('storage/' . $guest->nid_photo_back) }}" target="_blank" class="d-inline-block">
                    <img src="{{ asset('storage/' . $guest->nid_photo_back) }}" alt="NID Back" class="img-fluid rounded" style="max-height: 280px; object-fit: contain;">
                </a>
                <small class="d-block mt-1"><a href="{{ asset('storage/' . $guest->nid_photo_back) }}" target="_blank">Open full size</a></small>
            </div>
        </div>
    </div>
    @endif
</div>
@endif
<p>
    <a href="{{ route('guests.edit', $guest) }}" class="btn btn-primary">Edit</a>
    @if($guest->user_id)
        <form action="{{ route('guests.revoke-portal', $guest) }}" method="POST" class="d-inline"
              onsubmit="return confirm('Remove portal access for this guest?');">
            @csrf
            <button type="submit" class="btn btn-outline-danger btn-sm">Remove Portal Access</button>
        </form>
    @endif
</p>
<h5>Bookings</h5>
@foreach($guest->bookings as $b)
<p>{{ $b->booking_number }} Room {{ $b->room->number ?? '' }} <a href="{{ route('bookings.show', $b) }}">View</a></p>
@endforeach
@endsection
