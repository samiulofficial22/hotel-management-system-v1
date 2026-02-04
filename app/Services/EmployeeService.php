<?php

namespace App\Services;

use App\Models\Employee;
use App\Repositories\EmployeeRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class EmployeeService
{
    public function __construct(protected EmployeeRepository $repository) {}

    public function rules(bool $forUpdate = false): array
    {
        $empNumRule = $forUpdate ? 'required|string|max:30|unique:employees,employee_number,' : 'required|string|max:30|unique:employees,employee_number';
        return [
            'employee_number' => [$empNumRule],
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'department' => ['nullable', 'string', 'max:50'],
            'designation' => ['nullable', 'string', 'max:100'],
            'join_date' => ['nullable', 'date'],
            'base_salary' => ['nullable', 'numeric', 'min:0'],
            'phone' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email'],
            'is_active' => ['boolean'],
        ];
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
}
