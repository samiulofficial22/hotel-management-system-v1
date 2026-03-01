<?php

namespace App\Http\Controllers;

use App\Repositories\HousekeepingAssignmentRepository;
use App\Services\DashboardService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(protected
        DashboardService $dashboardService, protected
        HousekeepingAssignmentRepository $housekeepingRepo
        )
    {
    }

    /**
     * Dashboard: for Housekeeping role shows welcome + today's assignments; others get occupancy/revenue/alerts.
     */
    public function index(): View|\Illuminate\Http\RedirectResponse
    {
        if (Auth::user()->hasRole('Guest')) {
            return redirect()->route('guest.dashboard');
        }

        if (Auth::user()->hasRole('Housekeeping')) {
            $today = Carbon::today();
            $todayAssignments = $this->housekeepingRepo->forDate($today, (int)Auth::id());

            return view('dashboard.housekeeping', [
                'today' => $today,
                'todayAssignments' => $todayAssignments,
            ]);
        }

        $occupancy = $this->dashboardService->occupancyToday();
        $summary = $this->dashboardService->kpiSummary();
        $financial = $this->dashboardService->financialOverview();
        $roomBooking = $this->dashboardService->roomAndBookingSnapshot();
        $guestStaff = $this->dashboardService->guestAndStaffOverview();
        $pos = $this->dashboardService->posSnapshot();
        $alerts = $this->dashboardService->alerts();
        $extendedAlerts = $this->dashboardService->extendedAlerts();
        $charts = $this->dashboardService->chartsData();

        return view('dashboard.index', compact(
            'occupancy',
            'summary',
            'financial',
            'roomBooking',
            'guestStaff',
            'pos',
            'alerts',
            'extendedAlerts',
            'charts'
        ));
    }
}
