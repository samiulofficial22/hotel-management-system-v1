<?php

namespace App\Services;

use App\Models\Department;
use App\Repositories\DepartmentRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

/**
 * NEW – SAFE ADDITION: Department service. Soft validation only.
 */
class DepartmentService
{
    public function __construct(protected DepartmentRepository $repository) {}

    public function rules(bool $forUpdate = false, ?int $id = null): array
    {
        $nameUnique = $forUpdate && $id ? 'unique:departments,name,' . $id : 'unique:departments,name';
        $codeUnique = $forUpdate && $id ? 'unique:departments,code,' . $id : 'unique:departments,code';
        return [
            'name' => ['required', 'string', 'max:100', $nameUnique],
            'code' => ['required', 'string', 'max:50', $codeUnique],
            'description' => ['nullable', 'string', 'max:500'],
            'status' => ['required', 'in:active,inactive'],
        ];
    }

    public function all(bool $activeOnly = false): Collection
    {
        return $this->repository->all($activeOnly);
    }

    public function find(int $id): ?Department
    {
        return $this->repository->find($id);
    }

    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->paginate($perPage);
    }

    public function create(array $data): Department
    {
        $data['status'] = $data['status'] ?? Department::STATUS_ACTIVE;
        return $this->repository->create($data);
    }

    public function update(Department $department, array $data): Department
    {
        return $this->repository->update($department, $data);
    }

    public function delete(Department $department): bool
    {
        return $this->repository->delete($department);
    }
}
