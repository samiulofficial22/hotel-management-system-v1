<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Room;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardService
{
    /**
     * Occupancy: % of rooms occupied today (occupied status or has active booking for today).
     */
    public function occupancyToday(): array
    {
        $totalRooms = Room::where('is_active', true)->count();
        if ($totalRooms === 0) {
            return ['percentage' => 0, 'occupied' => 0, 'total' => 0];
        }
        $today = Carbon::today()->toDateString();
        $occupied = Booking::whereIn('status', [Booking::STATUS_CONFIRMED, Booking::STATUS_CHECKED_IN])
            ->where('check_in_date', '<=', $today)
            ->where('check_out_date', '>=', $today)
            ->distinct('room_id')
            ->count('room_id');
        $percentage = round(($occupied / $totalRooms) * 100, 1);
        return ['percentage' => $percentage, 'occupied' => $occupied, 'total' => $totalRooms];
    }

    /**
     * Revenue: sum of paid_amount from payments for current month (or today).
     */
    public function revenueThisMonth(): float
    {
        $sum = DB::table('payments')
            ->where('status', 'completed')
            ->whereMonth('payment_date', now()->month)
            ->whereYear('payment_date', now()->year)
            ->sum('amount');
        return round((float) $sum, 2);
    }

    public function revenueToday(): float
    {
        $sum = DB::table('payments')
            ->where('status', 'completed')
            ->whereDate('payment_date', now()->toDateString())
            ->sum('amount');
        return round((float) $sum, 2);
    }

    /**
     * Alerts: check-outs today, pending check-ins, rooms in cleaning.
     */
    public function alerts(): array
    {
        $today = Carbon::today()->toDateString();
        $checkOutsToday = Booking::with(['guest', 'room'])
            ->where('status', Booking::STATUS_CHECKED_IN)
            ->where('check_out_date', $today)
            ->orderBy('check_out_date')
            ->limit(10)
            ->get();
        $checkInsToday = Booking::with(['guest', 'room'])
            ->whereIn('status', [Booking::STATUS_CONFIRMED, Booking::STATUS_PENDING])
            ->where('check_in_date', $today)
            ->orderBy('check_in_date')
            ->limit(10)
            ->get();
        $roomsCleaning = Room::where('status', Room::STATUS_CLEANING)->count();
        return [
            'check_outs_today' => $checkOutsToday,
            'check_ins_today' => $checkInsToday,
            'rooms_cleaning' => $roomsCleaning,
        ];
    }
}
