<?php

namespace App\Repositories;

use App\Models\StoreItem;
use Illuminate\Database\Eloquent\Collection;

class StoreItemRepository
{
    public function __construct(protected StoreItem $model) {}

    public function all(bool $activeOnly = true): Collection
    {
        $q = $this->model->newQuery()->orderByDesc('id');
        if ($activeOnly) {
            $q->where('is_active', true);
        }
        return $q->get();
    }

    public function find(int $id): ?StoreItem
    {
        return $this->model->find($id);
    }

    public function create(array $data): StoreItem
    {
        return $this->model->create($data);
    }

    public function update(StoreItem $storeItem, array $data): StoreItem
    {
        $storeItem->update($data);
        return $storeItem->fresh();
    }
}
