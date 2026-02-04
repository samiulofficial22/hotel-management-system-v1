<?php

namespace App\Repositories;

use App\Models\MenuItem;
use Illuminate\Database\Eloquent\Collection;

class MenuItemRepository
{
    public function __construct(protected MenuItem $model) {}

    public function byCategory(int $categoryId, bool $availableOnly = true): Collection
    {
        $q = $this->model->newQuery()->where('menu_category_id', $categoryId)->orderBy('sort_order')->orderBy('name');
        if ($availableOnly) {
            $q->where('is_available', true);
        }
        return $q->get();
    }

    public function byOutlet(int $outletId, bool $availableOnly = true): Collection
    {
        $q = $this->model->newQuery()
            ->whereHas('menuCategory', fn ($q) => $q->where('outlet_id', $outletId))
            ->with('menuCategory')
            ->orderBy('menu_category_id')
            ->orderBy('sort_order')
            ->orderBy('name');
        if ($availableOnly) {
            $q->where('is_available', true);
        }
        return $q->get();
    }

    public function find(int $id): ?MenuItem
    {
        return $this->model->find($id);
    }

    public function create(array $data): MenuItem
    {
        return $this->model->create($data);
    }

    public function update(MenuItem $item, array $data): MenuItem
    {
        $item->update($data);
        return $item->fresh();
    }
}
