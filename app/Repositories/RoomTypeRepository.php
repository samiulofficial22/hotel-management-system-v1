<?php

namespace App\Repositories;

use App\Models\RoomType;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class RoomTypeRepository
{
    public function __construct(
        protected RoomType $model
    ) {}

    public function all(bool $activeOnly = false): Collection
    {
        $q = $this->model->newQuery()->orderBy('name');
        if ($activeOnly) {
            $q->where('is_active', true);
        }
        return $q->get();
    }

    public function find(int $id): ?RoomType
    {
        return $this->model->find($id);
    }

    public function findBySlug(string $slug): ?RoomType
    {
        return $this->model->where('slug', $slug)->first();
    }

    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->newQuery()->orderByDesc('id')->paginate($perPage);
    }

    public function create(array $data): RoomType
    {
        return $this->model->create($data);
    }

    public function update(RoomType $roomType, array $data): RoomType
    {
        $roomType->update($data);
        return $roomType->fresh();
    }

    public function delete(RoomType $roomType): bool
    {
        return $roomType->delete();
    }
}
