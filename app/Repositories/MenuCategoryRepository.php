<?php

namespace App\Repositories;

use App\Models\MenuCategory;
use Illuminate\Database\Eloquent\Collection;

class MenuCategoryRepository
{
    public function __construct(protected MenuCategory $model) {}

    public function byOutlet(int $outletId, bool $activeOnly = true): Collection
    {
        $q = $this->model->newQuery()->where('outlet_id', $outletId)->orderBy('sort_order')->orderBy('name');
        if ($activeOnly) {
            $q->where('is_active', true);
        }
        return $q->get();
    }

    public function find(int $id): ?MenuCategory
    {
        return $this->model->find($id);
    }

    public function create(array $data): MenuCategory
    {
        return $this->model->create($data);
    }

    public function update(MenuCategory $category, array $data): MenuCategory
    {
        $category->update($data);
        return $category->fresh();
    }
}
