<?php

namespace App\Services;

use App\Models\MenuItem;
use App\Repositories\MenuItemRepository;
use Illuminate\Database\Eloquent\Collection;

class MenuItemService
{
    public function __construct(protected MenuItemRepository $repository) {}

    public function rules(bool $forUpdate = false): array
    {
        return [
            'menu_category_id' => ['required', 'exists:menu_categories,id'],
            'name' => ['required', 'string', 'max:150'],
            'sku' => ['nullable', 'string', 'max:50'],
            'price' => ['required', 'numeric', 'min:0'],
            'cost' => ['nullable', 'numeric', 'min:0'],
            'description' => ['nullable', 'string'],
            'is_available' => ['boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ];
    }

    public function byOutlet(int $outletId, bool $availableOnly = true): Collection
    {
        return $this->repository->byOutlet($outletId, $availableOnly);
    }

    public function byCategory(int $categoryId, bool $availableOnly = true): Collection
    {
        return $this->repository->byCategory($categoryId, $availableOnly);
    }

    public function find(int $id): ?MenuItem
    {
        return $this->repository->find($id);
    }

    public function create(array $data): MenuItem
    {
        $data['is_available'] = $data['is_available'] ?? true;
        $data['sort_order'] = $data['sort_order'] ?? 0;
        return $this->repository->create($data);
    }

    public function update(MenuItem $item, array $data): MenuItem
    {
        return $this->repository->update($item, $data);
    }
}
