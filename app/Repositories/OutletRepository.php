<?php

namespace App\Repositories;

use App\Models\Outlet;
use Illuminate\Database\Eloquent\Collection;

class OutletRepository
{
    public function __construct(protected Outlet $model) {}

    public function all(bool $activeOnly = true): Collection
    {
        $q = $this->model->newQuery()->orderByDesc('id');
        if ($activeOnly) {
            $q->where('is_active', true);
        }
        return $q->get();
    }

    public function find(int $id): ?Outlet
    {
        return $this->model->find($id);
    }

    public function create(array $data): Outlet
    {
        return $this->model->create($data);
    }

    public function update(Outlet $outlet, array $data): Outlet
    {
        $outlet->update($data);
        return $outlet->fresh();
    }
}
