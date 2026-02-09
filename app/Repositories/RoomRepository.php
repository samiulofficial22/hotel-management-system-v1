<?php

namespace App\Repositories;

use App\Models\Room;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class RoomRepository
{
    public function __construct(
        protected Room $model
    ) {}

    public function all(bool $activeOnly = false): Collection
    {
        $q = $this->model->newQuery()->with('roomType')->orderBy('number');
        if ($activeOnly) {
            $q->where('is_active', true);
        }
        return $q->get();
    }

    public function find(int $id): ?Room
    {
        return $this->model->with('roomType')->find($id);
    }

    public function findByNumber(string $number): ?Room
    {
        return $this->model->where('number', $number)->first();
    }

    public function getByStatus(string $status): Collection
    {
        return $this->model->where('status', $status)->with('roomType')->orderBy('number')->get();
    }

    public function getAvailable(): Collection
    {
        return $this->getByStatus(Room::STATUS_AVAILABLE);
    }

    public function paginate(int $perPage = 15, ?int $roomTypeId = null): LengthAwarePaginator
    {
        $q = $this->model->newQuery()->with('roomType')->orderByDesc('id');
        if ($roomTypeId !== null) {
            $q->where('room_type_id', $roomTypeId);
        }
        return $q->paginate($perPage);
    }

    /**
     * Paginate with optional search (number, floor) and filters (room_type_id, status).
     */
    public function paginateWithFilters(int $perPage = 15, ?string $search = null, ?int $roomTypeId = null, ?string $status = null): LengthAwarePaginator
    {
        $q = $this->model->newQuery()->with('roomType')->orderByDesc('id');
        if ($search !== null && $search !== '') {
            $term = '%' . trim($search) . '%';
            $q->where(function ($query) use ($term) {
                $query->where('number', 'like', $term)
                    ->orWhere('floor', 'like', $term);
            });
        }
        if ($roomTypeId !== null && $roomTypeId !== '') {
            $q->where('room_type_id', $roomTypeId);
        }
        if ($status !== null && $status !== '') {
            $q->where('status', $status);
        }
        return $q->paginate($perPage)->withQueryString();
    }

    public function create(array $data): Room
    {
        return $this->model->create($data);
    }

    public function update(Room $room, array $data): Room
    {
        $room->update($data);
        return $room->fresh();
    }

    public function delete(Room $room): bool
    {
        return $room->delete();
    }
}
