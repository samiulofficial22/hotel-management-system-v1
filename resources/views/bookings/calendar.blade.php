@extends('layouts.app')
@section('title', 'Booking Calendar')
@section('content')
<div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-4">
    <h1 class="h3 mb-0">Booking Calendar</h1>
    <div class="d-flex gap-2 align-items-center">
        <a href="{{ route('bookings.index') }}" class="btn btn-outline-secondary">List</a>
        <a href="{{ route('bookings.calendar', ['start' => $start->copy()->subMonth()->startOfMonth()->format('Y-m-d'), 'end' => $start->copy()->subMonth()->endOfMonth()->format('Y-m-d')]) }}" class="btn btn-outline-primary">&larr; {{ $start->copy()->subMonth()->format('M Y') }}</a>
        <span class="fw-bold text-nowrap">{{ $start->format('F Y') }}</span>
        <a href="{{ route('bookings.calendar', ['start' => $start->copy()->addMonth()->startOfMonth()->format('Y-m-d'), 'end' => $start->copy()->addMonth()->endOfMonth()->format('Y-m-d')]) }}" class="btn btn-outline-primary">{{ $start->copy()->addMonth()->format('M Y') }} &rarr;</a>
    </div>
</div>

<div class="table-responsive bg-white rounded shadow-sm">
    <table class="table table-bordered mb-0 calendar-grid">
        <thead>
            <tr class="table-light">
                <th class="text-center" style="width:14.28%">Sun</th>
                <th class="text-center" style="width:14.28%">Mon</th>
                <th class="text-center" style="width:14.28%">Tue</th>
                <th class="text-center" style="width:14.28%">Wed</th>
                <th class="text-center" style="width:14.28%">Thu</th>
                <th class="text-center" style="width:14.28%">Fri</th>
                <th class="text-center" style="width:14.28%">Sat</th>
            </tr>
        </thead>
        <tbody>
            @php
                $firstDay = $start->copy()->startOfMonth();
                $lastDay = $start->copy()->endOfMonth();
                $startWeekday = (int) $firstDay->format('w');
                $calendarStart = $firstDay->copy()->subDays($startWeekday);
                $totalCells = 42;
                $days = [];
                for ($i = 0; $i < $totalCells; $i++) {
                    $days[] = $calendarStart->copy()->addDays($i);
                }
            @endphp
            @foreach (array_chunk($days, 7) as $week)
            <tr>
                @foreach ($week as $cellDay)
                @php
                    $isCurrentMonth = $cellDay->month === $firstDay->month;
                    $isToday = $cellDay->isToday();
                    $dayBookings = $bookings->filter(function ($b) use ($cellDay) {
                        return $cellDay->between($b->check_in_date->startOfDay(), $b->check_out_date->endOfDay());
                    });
                @endphp
                <td class="p-1 align-top calendar-day {{ !$isCurrentMonth ? 'text-muted bg-light' : '' }} {{ $isToday ? 'border-primary border-2' : '' }}" style="min-height: 100px; vertical-align: top;">
                    <div class="small fw-bold {{ $isToday ? 'text-primary' : '' }}">{{ $cellDay->format('j') }}</div>
                    @foreach ($dayBookings as $b)
                    <a href="{{ route('bookings.show', $b) }}" class="d-block small text-decoration-none rounded px-1 py-0 mb-1
                        @if($b->status === \App\Models\Booking::STATUS_CHECKED_IN) bg-success text-white
                        @elseif($b->status === \App\Models\Booking::STATUS_CONFIRMED || $b->status === \App\Models\Booking::STATUS_PENDING) bg-primary text-white
                        @elseif($b->status === \App\Models\Booking::STATUS_CHECKED_OUT) bg-secondary text-white
                        @else bg-dark text-white
                        @endif" title="{{ $b->guest->full_name ?? 'Guest' }} - {{ $b->room->number ?? '' }}">
                        {{ $b->room->number ?? '#' }}{{ $b->guest ? ' ' . \Illuminate\Support\Str::limit($b->guest->full_name, 12) : '' }}
                    </a>
                    @endforeach
                </td>
                @endforeach
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
<div class="mt-3 small text-muted">
    <span class="badge bg-primary">Confirmed/Pending</span>
    <span class="badge bg-success">Checked in</span>
    <span class="badge bg-secondary">Checked out</span>
</div>
@endsection
