<?php

namespace App\Services;

use App\Models\MenuCategory;
use App\Repositories\MenuCategoryRepository;
use Illuminate\Database\Eloquent\Collection;

class MenuCategoryService
{
    public function __construct(protected MenuCategoryRepository $repository) {}

    public function rules(): array
    {
        return [
            'outlet_id' => ['required', 'exists:outlets,id'],
            'name' => ['required', 'string', 'max:100'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['boolean'],
        ];
    }

    public function byOutlet(int $outletId, bool $activeOnly = true): Collection
    {
        return $this->repository->byOutlet($outletId, $activeOnly);
    }

    public function find(int $id): ?MenuCategory
    {
        return $this->repository->find($id);
    }

    public function create(array $data): MenuCategory
    {
        $data['sort_order'] = $data['sort_order'] ?? 0;
        $data['is_active'] = $data['is_active'] ?? true;
        return $this->repository->create($data);
    }

    public function update(MenuCategory $category, array $data): MenuCategory
    {
        return $this->repository->update($category, $data);
    }
}
