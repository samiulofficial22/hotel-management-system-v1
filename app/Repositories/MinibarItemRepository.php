<?php

namespace App\Repositories;

use App\Models\MinibarItem;
use Illuminate\Database\Eloquent\Collection;

class MinibarItemRepository
{
    public function __construct(protected MinibarItem $model) {}

    public function all(bool $activeOnly = true): Collection
    {
        $q = $this->model->newQuery()->orderBy('name');
        if ($activeOnly) {
            $q->where('is_active', true);
        }
        return $q->get();
    }

    public function find(int $id): ?MinibarItem
    {
        return $this->model->find($id);
    }

    public function create(array $data): MinibarItem
    {
        return $this->model->create($data);
    }

    public function update(MinibarItem $item, array $data): MinibarItem
    {
        $item->update($data);
        return $item->fresh();
    }
}
