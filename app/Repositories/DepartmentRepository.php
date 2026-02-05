<?php

namespace App\Repositories;

use App\Models\Department;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

/**
 * NEW – SAFE ADDITION: Department repository.
 */
class DepartmentRepository
{
    public function __construct(protected Department $model) {}

    public function all(bool $activeOnly = false): Collection
    {
        $q = $this->model->newQuery()->orderBy('name');
        if ($activeOnly) {
            $q->where('status', Department::STATUS_ACTIVE);
        }
        return $q->get();
    }

    public function find(int $id): ?Department
    {
        return $this->model->find($id);
    }

    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->newQuery()->orderBy('name')->paginate($perPage);
    }

    public function create(array $data): Department
    {
        return $this->model->create($data);
    }

    public function update(Department $department, array $data): Department
    {
        $department->update($data);
        return $department->fresh();
    }

    public function delete(Department $department): bool
    {
        return $department->delete();
    }
}
