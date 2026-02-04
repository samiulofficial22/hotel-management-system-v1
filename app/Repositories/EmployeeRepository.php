<?php

namespace App\Repositories;

use App\Models\Employee;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class EmployeeRepository
{
    public function __construct(protected Employee $model) {}

    public function all(bool $activeOnly = true): Collection
    {
        $q = $this->model->newQuery()->orderBy('employee_number');
        if ($activeOnly) {
            $q->where('is_active', true);
        }
        return $q->get();
    }

    public function find(int $id): ?Employee
    {
        return $this->model->find($id);
    }

    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->newQuery()->orderBy('employee_number')->paginate($perPage);
    }

    public function create(array $data): Employee
    {
        return $this->model->create($data);
    }

    public function update(Employee $employee, array $data): Employee
    {
        $employee->update($data);
        return $employee->fresh();
    }

    public function generateEmployeeNumber(): string
    {
        $last = $this->model->orderByDesc('id')->first();
        $seq = $last ? ((int) preg_replace('/\D/', '', $last->employee_number) + 1) : 1;
        return 'EMP' . str_pad((string) $seq, 4, '0', STR_PAD_LEFT);
    }
}
