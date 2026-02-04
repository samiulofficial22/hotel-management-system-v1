<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\ApiBaseController;
use App\Services\DashboardService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReportController extends ApiBaseController
{
    public function __construct(
        protected DashboardService $dashboardService
    ) {}

    public function dashboard(): JsonResponse
    {
        $occupancy = $this->dashboardService->occupancyToday();
        $revenueMonth = $this->dashboardService->revenueThisMonth();
        $revenueToday = $this->dashboardService->revenueToday();
        $alerts = $this->dashboardService->alerts();
        return $this->success([
            'occupancy' => $occupancy,
            'revenue_month' => $revenueMonth,
            'revenue_today' => $revenueToday,
            'alerts' => [
                'check_outs_today' => $alerts['check_outs_today'],
                'check_ins_today' => $alerts['check_ins_today'],
                'rooms_cleaning' => $alerts['rooms_cleaning'],
            ],
        ], 'Dashboard');
    }

    public function occupancy(Request $request): JsonResponse
    {
        $date = $request->input('date', now()->toDateString());
        $occupancy = $this->dashboardService->occupancyToday();
        return $this->success($occupancy, 'Occupancy');
    }

    public function revenue(Request $request): JsonResponse
    {
        $period = $request->input('period', 'month'); // today, month
        $revenue = $period === 'today' ? $this->dashboardService->revenueToday() : $this->dashboardService->revenueThisMonth();
        return $this->success(['revenue' => $revenue, 'period' => $period], 'Revenue');
    }
}
