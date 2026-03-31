<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\User;
use App\Services\DepartmentService;
use App\Services\EmployeeService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

class EmployeeController extends Controller
{
    public function __construct(
        protected EmployeeService $service,
        protected DepartmentService $departmentService
    ) {
    }

    public function index(): View
    {
        $employees = Employee::with('user')->orderByDesc('id')->paginate(15);
        return view('hr.employees.index', compact('employees'));
    }

    public function create(): View
    {
        $departments = $this->departmentService->all(true);
        $roles = Role::where('guard_name', config('auth.defaults.guard'))
            ->where('name', '!=', 'Guest')
            ->orderBy('name')
            ->get(['id', 'name']);
        return view('hr.employees.create', compact('departments', 'roles'));
    }

    public function store(Request $request): RedirectResponse
    {
        $rules = $this->service->rulesNewForm();
        $rules['password'] = ['nullable', 'string', 'min:8', 'confirmed'];
        $validated = $request->validate($rules);
        if (!empty($validated['password'])) {
            $request->validate(['email' => ['required', 'email', 'max:255', 'unique:users,email']]);
            $validated['email'] = $request->input('email');
        }
        $validated['department_id'] = $request->input('department_id') ?: null;

        if ($request->hasFile('nid_photo')) {
            $path = $request->file('nid_photo')->store('employees/nid', 'public');
            $validated['nid_photo'] = $path;
        }

        $data = $this->service->normalizeNewFormData($validated);
        $employee = $this->service->create($data);

        if (!empty($validated['password']) && !empty($validated['email'])) {
            $user = User::create([
                'name' => $employee->display_name,
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
            ]);
            if ($employee->designation && Role::where('guard_name', config('auth.defaults.guard'))->where('name', $employee->designation)->exists()) {
                $user->assignRole($employee->designation);
            }
            $employee->update(['user_id' => $user->id]);
        }

        if ($request->hasFile('profile_pic')) {
            $path = $request->file('profile_pic')->store('profile-pics', 'public');
            $employee->refresh();
            if ($employee->user_id && $employee->user) {
                $employee->user->update(['profile_pic' => $path]);
            } else {
                $employee->update(['photo' => $path]);
            }
        }

        return redirect()->route('hr.employees.index')->with('success', __('Employee created.'));
    }

    public function show(Employee $employee): View
    {
        $employee->load('user');
        return view('hr.employees.show', compact('employee'));
    }

    public function edit(Employee $employee): View
    {
        $departments = $this->departmentService->all(true);
        $roles = Role::where('guard_name', config('auth.defaults.guard'))
            ->where('name', '!=', 'Guest')
            ->orderBy('name')
            ->get(['id', 'name']);
        return view('hr.employees.edit', compact('employee', 'departments', 'roles'));
    }

    public function update(Request $request, Employee $employee): RedirectResponse
    {
        $rules = $this->service->rulesNewForm(true);
        $rules['password'] = ['nullable', 'string', 'min:8', 'confirmed'];
        $validated = $request->validate($rules);

        if (!empty($validated['password'])) {
            $emailRules = ['required', 'email', 'max:255'];
            if ($employee->user_id && $employee->user) {
                $emailRules[] = 'unique:users,email,' . $employee->user_id;
            } else {
                $emailRules[] = 'unique:users,email';
            }
            $request->validate(['email' => $emailRules]);
            $validated['email'] = $request->input('email');
        }
        $validated['department_id'] = $request->input('department_id') ?: null;

        if ($request->hasFile('nid_photo')) {
            if ($employee->nid_photo) {
                Storage::disk('public')->delete($employee->nid_photo);
            }
            $path = $request->file('nid_photo')->store('employees/nid', 'public');
            $validated['nid_photo'] = $path;
        }

        $data = $this->service->normalizeNewFormData($validated, $employee);
        $this->service->update($employee, $data);

        if (!empty($validated['password'])) {
            $email = $validated['email'] ?? $employee->email ?? $employee->user?->email;
            if ($employee->user_id && $employee->user) {
                $employee->user->update([
                    'password' => Hash::make($validated['password']),
                    'name' => $employee->fresh()->display_name,
                    'email' => $email,
                ]);
                if ($employee->designation && Role::where('guard_name', config('auth.defaults.guard'))->where('name', $employee->designation)->exists()) {
                    $employee->user->syncRoles([$employee->designation]);
                }
            } elseif ($email) {
                $user = User::create([
                    'name' => $employee->fresh()->display_name,
                    'email' => $email,
                    'password' => Hash::make($validated['password']),
                ]);
                if ($employee->designation && Role::where('guard_name', config('auth.defaults.guard'))->where('name', $employee->designation)->exists()) {
                    $user->assignRole($employee->designation);
                }
                $employee->update(['user_id' => $user->id]);
            }
        }

        if ($request->hasFile('profile_pic')) {
            $path = $request->file('profile_pic')->store('profile-pics', 'public');
            $employee->refresh();
            if ($employee->user_id && $employee->user) {
                if ($employee->user->profile_pic) {
                    Storage::disk('public')->delete($employee->user->profile_pic);
                }
                $employee->user->update(['profile_pic' => $path]);
            } else {
                if ($employee->photo) {
                    Storage::disk('public')->delete($employee->photo);
                }
                $employee->update(['photo' => $path]);
            }
        }

        return redirect()->route('hr.employees.index')->with('success', __('Employee updated.'));
    }

    public function destroy(Employee $employee): RedirectResponse
    {
        $this->service->delete($employee);
        return redirect()->route('hr.employees.index')->with('success', __('Employee deleted.'));
    }

    public function syncToUsers(): RedirectResponse
    {
        $result = $this->service->syncEmployeesToUsers();
        $msg = [];
        if ($result['created'] > 0) {
            $msg[] = __(':count user(s) created.', ['count' => $result['created']]);
        }
        if ($result['updated'] > 0) {
            $msg[] = __(':count linked/updated.', ['count' => $result['updated']]);
        }
        if ($result['skipped'] > 0) {
            $msg[] = __(':count skipped.', ['count' => $result['skipped']]);
        }
        $message = implode(' ', $msg) ?: __('No changes.');
        if (!empty($result['errors'])) {
            return redirect()->route('hr.employees.index')
                ->with('sync_result', $message)
                ->with('sync_errors', $result['errors']);
        }
        return redirect()->route('hr.employees.index')->with('success', $message);
    }
}
