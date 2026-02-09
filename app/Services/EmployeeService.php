<?php

namespace App\Services;

use App\Models\Employee;
use App\Models\User;
use App\Repositories\EmployeeRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class EmployeeService
{
    public function __construct(protected EmployeeRepository $repository) {}

    /** Validation rules for legacy form (unchanged). */
    public function rules(bool $forUpdate = false): array
    {
        $empNumRules = ['required', 'string', 'max:30'];
        $empNumRules[] = $forUpdate ? 'unique:employees,employee_number,' . (request()->route('employee')?->id ?? '') : 'unique:employees,employee_number';
        return [
            'employee_number' => $empNumRules,
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'department' => ['nullable', 'string', 'max:50'],
            'department_id' => ['nullable', 'integer', 'exists:departments,id'],
            'designation' => ['nullable', 'string', 'max:100'],
            'join_date' => ['nullable', 'date'],
            'base_salary' => ['nullable', 'numeric', 'min:0'],
            'phone' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email'],
            'is_active' => ['boolean'],
        ];
    }

    /** Validation rules for new Employee module form: only name required; all others nullable. */
    public function rulesNewForm(bool $forUpdate = false): array
    {
        $empCodeRules = ['nullable', 'string', 'max:30'];
        $empCodeRules[] = $forUpdate
            ? 'unique:employees,employee_code,' . (request()->route('employee')?->id ?? '')
            : 'unique:employees,employee_code';

        return [
            'name' => ['required', 'string', 'max:200'],
            'employee_code' => $empCodeRules,
            'phone' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:255'],
            'designation' => ['nullable', 'string', 'max:100'],
            'department_id' => ['nullable', 'integer', 'exists:departments,id'],
            'employment_type' => ['nullable', 'string', 'max:50'],
            'join_date' => ['nullable', 'date'],
            'salary' => ['nullable', 'numeric', 'min:0'],
            'shift' => ['nullable', 'string', 'max:50'],
            'status' => ['nullable', 'string', 'max:30'],
            'nid_number' => ['nullable', 'string', 'max:50'],
            'nid_photo' => ['nullable', 'image', 'mimes:jpeg,jpg,png', 'max:2048'],
        ];
    }

    /** Normalize new-form data into model attributes (including legacy columns for existing logic). */
    public function normalizeNewFormData(array $data, ?Employee $employee = null): array
    {
        $name = trim($data['name'] ?? '');
        $parts = $name !== '' ? explode(' ', $name, 2) : ['', ''];
        $firstName = $parts[0] ?? '';
        $lastName = $parts[1] ?? '';

        $employeeCode = isset($data['employee_code']) && trim((string) $data['employee_code']) !== ''
            ? trim($data['employee_code'])
            : null;
        $employeeNumber = $employeeCode ?? ($employee ? $employee->employee_number : $this->repository->generateEmployeeNumber());
        if ($employeeCode === null) {
            $employeeCode = $employeeNumber;
        }

        $status = $data['status'] ?? null;
        $isActive = $status !== null ? (strtolower((string) $status) === 'active') : true;

        $out = [
            'first_name' => $firstName,
            'last_name' => $lastName,
            'employee_number' => $employeeNumber,
            'employee_code' => $employeeCode,
            'name' => $name !== '' ? $name : null,
            'phone' => $data['phone'] ?? null,
            'email' => $data['email'] ?? null,
            'designation' => $data['designation'] ?? null,
            'department_id' => isset($data['department_id']) && $data['department_id'] !== '' ? (int) $data['department_id'] : null,
            'employment_type' => $data['employment_type'] ?? null,
            'join_date' => $data['join_date'] ?? null,
            'salary' => isset($data['salary']) && $data['salary'] !== '' ? $data['salary'] : null,
            'shift' => $data['shift'] ?? null,
            'status' => $status,
            'is_active' => $isActive,
            'base_salary' => isset($data['salary']) && $data['salary'] !== '' ? (float) $data['salary'] : ($employee->base_salary ?? 0),
        ];
        $out['nid_number'] = isset($data['nid_number']) && trim((string) $data['nid_number']) !== '' ? trim($data['nid_number']) : null;
        if (array_key_exists('nid_photo', $data) && $data['nid_photo'] !== null) {
            $out['nid_photo'] = $data['nid_photo'];
        }
        return $out;
    }

    public function all(bool $activeOnly = true): Collection
    {
        return $this->repository->all($activeOnly);
    }

    public function find(int $id): ?Employee
    {
        return $this->repository->find($id);
    }

    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->paginate($perPage);
    }

    public function create(array $data): Employee
    {
        if (empty($data['employee_number'])) {
            $data['employee_number'] = $this->repository->generateEmployeeNumber();
        }
        $data['is_active'] = $data['is_active'] ?? true;
        $data['base_salary'] = $data['base_salary'] ?? 0;
        return $this->repository->create($data);
    }

    public function update(Employee $employee, array $data): Employee
    {
        return $this->repository->update($employee, $data);
    }

    public function delete(Employee $employee): void
    {
        if ($employee->photo) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($employee->photo);
        }
        if ($employee->nid_photo) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($employee->nid_photo);
        }
        $employee->delete();
    }

    /**
     * Sync employees to users table (office staff).
     * - Employees without user_id: create User (name, email, random password), assign role = designation, link employee.user_id.
     * - Employees with user_id: sync user's role to match designation.
     * Returns ['created' => int, 'updated' => int, 'skipped' => int, 'errors' => array].
     */
    public function syncEmployeesToUsers(): array
    {
        $guard = config('auth.defaults.guard');
        $employees = $this->repository->all(false);
        $created = 0;
        $updated = 0;
        $skipped = 0;
        $errors = [];

        foreach ($employees as $employee) {
            $name = $employee->display_name;
            if (trim($name ?? '') === '') {
                $skipped++;
                $errors[] = __('Employee :code has no name.', ['code' => $employee->employee_code ?? $employee->id]);
                continue;
            }

            if ($employee->user_id) {
                $user = $employee->user;
                if (! $user) {
                    $employee->update(['user_id' => null]);
                    $updated++;
                    continue;
                }
                if ($employee->designation && Role::where('guard_name', $guard)->where('name', $employee->designation)->exists()) {
                    $user->syncRoles([$employee->designation]);
                }
                $updated++;
                continue;
            }

            $email = trim($employee->email ?? '');
            if ($email === '') {
                $base = Str::slug($employee->employee_code ?? 'emp' . $employee->id);
                $email = strtolower($base) . '@staff.sync';
                $counter = 0;
                while (User::where('email', $email)->exists()) {
                    $counter++;
                    $email = strtolower($base) . $counter . '@staff.sync';
                }
            } else {
                $existingUser = User::where('email', $email)->first();
                if ($existingUser) {
                    $employee->update(['user_id' => $existingUser->id]);
                    if ($employee->designation && Role::where('guard_name', $guard)->where('name', $employee->designation)->exists()) {
                        $existingUser->syncRoles([$employee->designation]);
                    }
                    $updated++;
                    continue;
                }
            }

            try {
                $user = User::create([
                    'name' => $name,
                    'email' => $email,
                    'password' => Hash::make(Str::random(16)),
                ]);
                if ($employee->designation && Role::where('guard_name', $guard)->where('name', $employee->designation)->exists()) {
                    $user->assignRole($employee->designation);
                }
                $employee->update(['user_id' => $user->id]);
                $created++;
            } catch (\Throwable $e) {
                $skipped++;
                $errors[] = __('Employee :name: :message', ['name' => $name, 'message' => $e->getMessage()]);
            }
        }

        return ['created' => $created, 'updated' => $updated, 'skipped' => $skipped, 'errors' => $errors];
    }
}
