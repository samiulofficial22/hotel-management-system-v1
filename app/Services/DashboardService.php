<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\Booking;
use App\Models\Employee;
use App\Models\Invoice;
use App\Models\MinibarItem;
use App\Models\PayrollRun;
use App\Models\PosOrder;
use App\Models\Room;
use App\Models\StoreItem;
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
     * Revenue: sum of completed payments for current month (or today).
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

    /**
     * Top summary KPIs for dashboard cards.
     */
    public function kpiSummary(): array
    {
        $today = Carbon::today()->toDateString();

        $totalRooms = Room::where('is_active', true)->count();
        $availableRooms = Room::where('is_active', true)
            ->where('status', Room::STATUS_AVAILABLE)
            ->count();
        $occupiedRooms = Room::where('is_active', true)
            ->where('status', Room::STATUS_OCCUPIED)
            ->count();

        $todayCheckins = Booking::whereIn('status', [Booking::STATUS_CONFIRMED, Booking::STATUS_CHECKED_IN])
            ->where('check_in_date', $today)
            ->count();
        $todayCheckouts = Booking::where('status', Booking::STATUS_CHECKED_IN)
            ->where('check_out_date', $today)
            ->count();
        $pendingBookingRequests = Booking::where('status', Booking::STATUS_PENDING)->count();

        return [
            'total_rooms' => $totalRooms,
            'available_rooms' => $availableRooms,
            'occupied_rooms' => $occupiedRooms,
            'today_checkins' => $todayCheckins,
            'today_checkouts' => $todayCheckouts,
            'pending_bookings' => $pendingBookingRequests,
        ];
    }

    /**
     * Financial overview metrics: revenue, dues, POS, payroll.
     */
    public function financialOverview(): array
    {
        $today = now()->toDateString();
        $month = now()->month;
        $year = now()->year;

        $todayRevenue = $this->revenueToday();
        $monthRevenue = $this->revenueThisMonth();

        $outstandingDues = (float) Invoice::where('balance_due', '>', 0)->sum('balance_due');

        $todayPosSales = (float) DB::table('payments')
            ->where('status', 'completed')
            ->whereNotNull('pos_order_id')
            ->whereDate('payment_date', $today)
            ->sum('amount');

        $paidRuns = PayrollRun::where('status', PayrollRun::STATUS_PAID)
            ->whereNotNull('paid_at')
            ->whereMonth('paid_at', $month)
            ->whereYear('paid_at', $year)
            ->with('items')
            ->get();
        $monthlyPayroll = $paidRuns->sum(fn ($run) => $run->items->sum('net_salary'));

        return [
            'today_revenue' => round($todayRevenue, 2),
            'month_revenue' => round($monthRevenue, 2),
            'outstanding_dues' => round($outstandingDues, 2),
            'today_pos_sales' => round($todayPosSales, 2),
            'monthly_payroll' => round((float) $monthlyPayroll, 2),
        ];
    }

    /**
     * Room and booking snapshot.
     */
    public function roomAndBookingSnapshot(): array
    {
        $today = Carbon::today()->toDateString();

        $roomsByStatus = [
            'available' => Room::where('status', Room::STATUS_AVAILABLE)->count(),
            'occupied' => Room::where('status', Room::STATUS_OCCUPIED)->count(),
            'maintenance' => Room::where('status', Room::STATUS_MAINTENANCE)->count(),
            'cleaning' => Room::where('status', Room::STATUS_CLEANING)->count(),
        ];

        // Reserved rooms approximated from bookings with future/today stays
        $reservedRooms = Booking::whereIn('status', [Booking::STATUS_CONFIRMED, Booking::STATUS_PENDING])
            ->where('check_in_date', '>=', $today)
            ->distinct('room_id')
            ->count('room_id');
        $roomsByStatus['reserved'] = $reservedRooms;

        $latestBookings = Booking::with(['guest', 'room'])
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        $pendingApprovals = Booking::with(['guest', 'room'])
            ->where('status', Booking::STATUS_PENDING)
            ->orderBy('check_in_date')
            ->limit(10)
            ->get();

        return [
            'rooms_by_status' => $roomsByStatus,
            'latest_bookings' => $latestBookings,
            'pending_approvals' => $pendingApprovals,
        ];
    }

    /**
     * Guest and staff overview for dashboard.
     */
    public function guestAndStaffOverview(): array
    {
        $today = Carbon::today()->toDateString();

        $currentStayingGuests = Booking::where('status', Booking::STATUS_CHECKED_IN)
            ->where('check_in_date', '<=', $today)
            ->where('check_out_date', '>=', $today)
            ->distinct('guest_id')
            ->count('guest_id');

        $pendingGuestApprovals = Booking::where('status', Booking::STATUS_PENDING)->count();

        $totalEmployees = Employee::where('is_active', true)->count();

        $attendanceToday = Attendance::whereDate('date', $today)->get();
        $presentToday = $attendanceToday
            ->whereIn('status', [Attendance::STATUS_PRESENT, Attendance::STATUS_HALF_DAY])
            ->pluck('employee_id')
            ->unique()
            ->count();
        $absentToday = max($totalEmployees - $presentToday, 0);

        $payrollPending = PayrollRun::whereIn('status', [PayrollRun::STATUS_DRAFT, PayrollRun::STATUS_PROCESSED])->count();

        return [
            'current_staying_guests' => $currentStayingGuests,
            'pending_guest_approvals' => $pendingGuestApprovals,
            'total_employees' => $totalEmployees,
            'present_today' => $presentToday,
            'absent_today' => $absentToday,
            'payroll_pending' => $payrollPending,
        ];
    }

    /**
     * POS snapshot: orders and revenue.
     */
    public function posSnapshot(): array
    {
        $today = Carbon::today();

        $todayOrdersCount = PosOrder::whereDate('created_at', $today->toDateString())
            ->whereNotIn('payment_status', [PosOrder::PAYMENT_STATUS_VOIDED])
            ->count();

        $posRevenueByType = PosOrder::selectRaw('pos_type, SUM(total) as total')
            ->whereDate('created_at', $today->toDateString())
            ->whereNotIn('payment_status', [PosOrder::PAYMENT_STATUS_VOIDED])
            ->groupBy('pos_type')
            ->pluck('total', 'pos_type')
            ->toArray();

        // Room charges pending to be settled = invoices with balance_due > 0
        $roomChargesPending = (float) Invoice::where('balance_due', '>', 0)->sum('balance_due');

        return [
            'today_orders' => $todayOrdersCount,
            'revenue_by_type' => $posRevenueByType,
            'room_charges_pending' => round($roomChargesPending, 2),
        ];
    }

    /**
     * Alerts & action items beyond basic alerts().
     */
    public function extendedAlerts(): array
    {
        $unpaidInvoices = Invoice::where('balance_due', '>', 0)
            ->orderByDesc('invoice_date')
            ->limit(10)
            ->get();

        $lowMinibarItems = MinibarItem::where('is_active', true)
            ->whereColumn('quantity_in_stock', '<=', 'reorder_level')
            ->orderBy('name')
            ->limit(10)
            ->get();

        $lowStoreItems = StoreItem::where('is_active', true)
            ->whereColumn('quantity', '<=', 'reorder_level')
            ->orderBy('name')
            ->limit(10)
            ->get();

        $unpaidPayrollRuns = PayrollRun::whereIn('status', [PayrollRun::STATUS_DRAFT, PayrollRun::STATUS_PROCESSED])
            ->orderBy('period_end', 'desc')
            ->limit(5)
            ->get();

        return [
            'unpaid_invoices' => $unpaidInvoices,
            'low_minibar_items' => $lowMinibarItems,
            'low_store_items' => $lowStoreItems,
            'unpaid_payroll_runs' => $unpaidPayrollRuns,
        ];
    }

    /**
     * Chart data for dashboard visuals.
     */
    public function chartsData(): array
    {
        // Revenue trend (last 7 days)
        $days = 7;
        $to = Carbon::today();
        $from = $to->copy()->subDays($days - 1);

        $paymentSums = DB::table('payments')
            ->where('status', 'completed')
            ->whereBetween('payment_date', [$from->toDateString(), $to->toDateString()])
            ->selectRaw('DATE(payment_date) as d, SUM(amount) as total')
            ->groupBy('d')
            ->pluck('total', 'd')
            ->toArray();

        $revenueLabels = [];
        $revenueValues = [];
        $cursor = $from->copy();
        while ($cursor->lte($to)) {
            $key = $cursor->toDateString();
            $revenueLabels[] = $cursor->format('M d');
            $revenueValues[] = isset($paymentSums[$key]) ? (float) $paymentSums[$key] : 0;
            $cursor->addDay();
        }

        // POS vs Room revenue (this month)
        $month = now()->month;
        $year = now()->year;
        $posRevenue = (float) DB::table('payments')
            ->where('status', 'completed')
            ->whereNotNull('pos_order_id')
            ->whereMonth('payment_date', $month)
            ->whereYear('payment_date', $year)
            ->sum('amount');
        $allRevenue = $this->revenueThisMonth();
        $roomRevenue = max($allRevenue - $posRevenue, 0);

        // Expense vs Income (this month) – approximate using ledger revenue/expense helpers
        $fromMonth = Carbon::now()->copy()->startOfMonth();
        $toMonth = Carbon::now()->copy()->endOfMonth();

        // These totals are already exposed via AccountsController, reuse LedgerEntryService there instead
        // but here we only need approximate chart; revenue = allRevenue, expense from ledger service if needed.
        // For now, use LedgerEntryService via app() to avoid hard coupling.
        $ledger = app(LedgerEntryService::class);
        $totalRevenue = $ledger->totalRevenue($fromMonth, $toMonth);
        $totalExpense = $ledger->totalExpense($fromMonth, $toMonth);

        return [
            'revenue_trend' => [
                'labels' => $revenueLabels,
                'values' => $revenueValues,
            ],
            'pos_vs_room' => [
                'pos' => round($posRevenue, 2),
                'room' => round($roomRevenue, 2),
            ],
            'expense_vs_income' => [
                'revenue' => round($totalRevenue, 2),
                'expense' => round($totalExpense, 2),
            ],
        ];
    }
}
