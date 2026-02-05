<?php

namespace App\Http\Controllers;

use App\Services\DashboardService;
use App\Services\DepartmentService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function __construct(
        protected DashboardService $dashboardService,
        protected DepartmentService $departmentService
    ) {}

    public function index(): View
    {
        $departmentFilterEnabled = config('hotel.department_filter_enabled', false);
        $departments = $departmentFilterEnabled ? $this->departmentService->all(false) : collect();
        return view('reports.index', compact('departments', 'departmentFilterEnabled'));
    }

    public function dashboard(): View
    {
        $occupancy = $this->dashboardService->occupancyToday();
        $revenueMonth = $this->dashboardService->revenueThisMonth();
        $revenueToday = $this->dashboardService->revenueToday();
        $alerts = $this->dashboardService->alerts();
        return view('reports.dashboard', compact('occupancy', 'revenueMonth', 'revenueToday', 'alerts'));
    }

    public function occupancy(Request $request): View
    {
        $date = $request->has('date') ? Carbon::parse($request->date) : today();
        $occupancy = $this->dashboardService->occupancyToday();
        return view('reports.occupancy', compact('occupancy', 'date'));
    }

    public function revenue(Request $request): View
    {
        $period = $request->input('period', 'month');
        $revenue = $period === 'today' ? $this->dashboardService->revenueToday() : $this->dashboardService->revenueThisMonth();
        return view('reports.revenue', compact('revenue', 'period'));
    }
}
