<?php

namespace App\Http\Controllers;

use App\Services\DashboardService;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(
        protected DashboardService $dashboardService
    ) {}

    /**
     * Dashboard: occupancy, revenue, alerts (check-ins/check-outs today, rooms cleaning).
     */
    public function index(): View
    {
        $occupancy = $this->dashboardService->occupancyToday();
        $revenueMonth = $this->dashboardService->revenueThisMonth();
        $revenueToday = $this->dashboardService->revenueToday();
        $alerts = $this->dashboardService->alerts();

        return view('dashboard.index', [
            'occupancy' => $occupancy,
            'revenueMonth' => $revenueMonth,
            'revenueToday' => $revenueToday,
            'alerts' => $alerts,
        ]);
    }
}
