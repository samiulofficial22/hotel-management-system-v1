<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Services\DepartmentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * NEW – SAFE ADDITION: Department CRUD. Settings → Departments.
 */
class DepartmentController extends Controller
{
    public function __construct(protected DepartmentService $service) {}

    public function index(Request $request): View
    {
        $departments = $this->service->paginate($request->integer('per_page', 15));
        return view('departments.index', compact('departments'));
    }

    public function create(): View
    {
        return view('departments.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate($this->service->rules());
        $this->service->create($validated);
        return redirect()->route('settings.departments.index')->with('success', __('Department created.'));
    }

    public function edit(Department $department): View
    {
        return view('departments.edit', compact('department'));
    }

    public function update(Request $request, Department $department): RedirectResponse
    {
        $validated = $request->validate($this->service->rules(true, $department->id));
        $this->service->update($department, $validated);
        return redirect()->route('settings.departments.index')->with('success', __('Department updated.'));
    }

    public function destroy(Department $department): RedirectResponse
    {
        $this->service->delete($department);
        return redirect()->route('settings.departments.index')->with('success', __('Department deleted.'));
    }
}
