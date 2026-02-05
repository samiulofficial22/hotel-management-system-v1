<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Services\DepartmentService;
use App\Services\EmployeeService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class EmployeeController extends Controller
{
    public function __construct(
        protected EmployeeService $service,
        protected DepartmentService $departmentService
    ) {}

    public function index(): View
    {
        $employees = $this->service->paginate(15);
        return view('hr.employees.index', compact('employees'));
    }

    public function create(): View
    {
        $departments = $this->departmentService->all(true);
        return view('hr.employees.create', compact('departments'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate($this->service->rulesNewForm());
        $validated['department_id'] = $request->input('department_id') ?: null;

        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('employees', 'public');
            $validated['photo'] = $path;
        }
        if ($request->hasFile('nid_photo')) {
            $path = $request->file('nid_photo')->store('employees/nid', 'public');
            $validated['nid_photo'] = $path;
        }

        $data = $this->service->normalizeNewFormData($validated);
        $this->service->create($data);
        return redirect()->route('hr.employees.index')->with('success', __('Employee created.'));
    }

    public function show(Employee $employee): View
    {
        return view('hr.employees.show', compact('employee'));
    }

    public function edit(Employee $employee): View
    {
        $departments = $this->departmentService->all(true);
        return view('hr.employees.edit', compact('employee', 'departments'));
    }

    public function update(Request $request, Employee $employee): RedirectResponse
    {
        $validated = $request->validate($this->service->rulesNewForm(true));
        $validated['department_id'] = $request->input('department_id') ?: null;

        if ($request->hasFile('photo')) {
            if ($employee->photo) {
                Storage::disk('public')->delete($employee->photo);
            }
            $path = $request->file('photo')->store('employees', 'public');
            $validated['photo'] = $path;
        }
        if ($request->hasFile('nid_photo')) {
            if ($employee->nid_photo) {
                Storage::disk('public')->delete($employee->nid_photo);
            }
            $path = $request->file('nid_photo')->store('employees/nid', 'public');
            $validated['nid_photo'] = $path;
        }

        $data = $this->service->normalizeNewFormData($validated, $employee);
        $this->service->update($employee, $data);
        return redirect()->route('hr.employees.show', $employee)->with('success', __('Employee updated.'));
    }
}
