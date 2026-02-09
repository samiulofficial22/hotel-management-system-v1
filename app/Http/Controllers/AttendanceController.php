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
        $attendancesByEmployee = $attendances->keyBy('employee_id');
        return view('hr.attendance.index', compact('attendances', 'employees', 'date', 'attendancesByEmployee'));
    }

    public function mark(Request $request): RedirectResponse
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'date' => 'required|date',
            'check_in' => 'nullable|string|max:10',
            'check_out' => 'nullable|string|max:10',
            'status' => 'nullable|in:present,absent,half_day,leave',
            'notes' => 'nullable|string|max:500',
        ]);
        $this->service->markAttendance(
            (int) $request->employee_id,
            Carbon::parse($request->date),
            $request->check_in ?: null,
            $request->check_out ?: null,
            $request->status ?? 'present',
            $request->notes
        );
        return redirect()->route('hr.attendance.index', ['date' => $request->date])->with('success', __('Attendance updated.'));
    }
}
