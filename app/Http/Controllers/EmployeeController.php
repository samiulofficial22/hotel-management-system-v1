<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Services\EmployeeService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Validation\ValidationException;

class EmployeeController extends Controller
{
    public function __construct(protected EmployeeService $service) {}

    public function index(): View
    {
        $employees = $this->service->paginate(15);
        return view('hr.employees.index', compact('employees'));
    }

    public function create(): View
    {
        return view('hr.employees.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate($this->service->rules());
        $this->service->create($validated);
        return redirect()->route('hr.employees.index')->with('success', 'Employee created.');
    }

    public function show(Employee $employee): View
    {
        return view('hr.employees.show', compact('employee'));
    }

    public function edit(Employee $employee): View
    {
        return view('hr.employees.edit', compact('employee'));
    }

    public function update(Request $request, Employee $employee): RedirectResponse
    {
        $validated = $request->validate($this->service->rules(true));
        $this->service->update($employee, $validated);
        return redirect()->route('hr.employees.show', $employee)->with('success', 'Employee updated.');
    }
}
