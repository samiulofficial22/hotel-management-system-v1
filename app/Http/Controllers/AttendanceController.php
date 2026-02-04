<?php

namespace App\Http\Controllers;

use App\Services\AttendanceService;
use App\Services\EmployeeService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AttendanceController extends Controller
{
    public function __construct(
        protected AttendanceService $service,
        protected EmployeeService $employeeService
    ) {}

    public function index(Request $request): View
    {
        $date = $request->has('date') ? Carbon::parse($request->date) : today();
        $attendances = $this->service->forDate($date);
        $employees = $this->employeeService->all(true);
        return view('hr.attendance.index', compact('attendances', 'employees', 'date'));
    }

    public function mark(Request $request): RedirectResponse
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'date' => 'required|date',
            'check_in' => 'nullable',
            'check_out' => 'nullable',
            'status' => 'nullable|in:present,absent,half_day,leave',
        ]);
        $this->service->markAttendance(
            (int) $request->employee_id,
            Carbon::parse($request->date),
            $request->check_in,
            $request->check_out,
            $request->status ?? 'present'
        );
        return redirect()->route('hr.attendance.index', ['date' => $request->date])->with('success', 'Attendance updated.');
    }
}
