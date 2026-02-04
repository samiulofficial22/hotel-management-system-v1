<?php

namespace App\Services;

use App\Models\RoomType;
use App\Repositories\RoomTypeRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class RoomTypeService
{
    public function __construct(
        protected RoomTypeRepository $repository
    ) {}

    public function rules(bool $forUpdate = false): array
    {
        $unique = $forUpdate ? 'unique:room_types,slug,' : 'unique:room_types,slug';
        return [
            'name' => ['required', 'string', 'max:100'],
            'slug' => ['nullable', 'string', 'max:100', $unique],
            'description' => ['nullable', 'string'],
            'base_rate' => ['required', 'numeric', 'min:0'],
            'max_occupancy' => ['required', 'integer', 'min:1', 'max:20'],
            'size_sqm' => ['nullable', 'integer', 'min:1'],
            'is_active' => ['boolean'],
        ];
    }

    public function all(bool $activeOnly = false): Collection
    {
        return $this->repository->all($activeOnly);
    }

    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->paginate($perPage);
    }

    public function find(int $id): ?RoomType
    {
        return $this->repository->find($id);
    }

    /** @throws ValidationException */
    public function create(array $data): RoomType
    {
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }
        $data['is_active'] = $data['is_active'] ?? true;
        return $this->repository->create($data);
    }

    /** @throws ValidationException */
    public function update(RoomType $roomType, array $data): RoomType
    {
        if (isset($data['name']) && empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }
        return $this->repository->update($roomType, $data);
    }

    public function delete(RoomType $roomType): bool
    {
        return $this->repository->delete($roomType);
    }
}
