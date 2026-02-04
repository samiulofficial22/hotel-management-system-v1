<?php

namespace App\Services;

use App\Models\Room;
use App\Repositories\RoomRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Validation\ValidationException;

class RoomService
{
    public function __construct(
        protected RoomRepository $repository
    ) {}

    public function rules(bool $forUpdate = false): array
    {
        $unique = $forUpdate ? 'unique:rooms,number,' : 'unique:rooms,number';
        return [
            'room_type_id' => ['required', 'exists:room_types,id'],
            'number' => ['required', 'string', 'max:20', $unique],
            'floor' => ['nullable', 'string', 'max:10'],
            'status' => ['nullable', 'in:available,occupied,cleaning,maintenance,out_of_order'],
            'notes' => ['nullable', 'string'],
            'is_active' => ['boolean'],
        ];
    }

    public function all(bool $activeOnly = false): Collection
    {
        return $this->repository->all($activeOnly);
    }

    public function getAvailable(): Collection
    {
        return $this->repository->getAvailable();
    }

    public function getByStatus(string $status): Collection
    {
        return $this->repository->getByStatus($status);
    }

    public function paginate(int $perPage = 15, ?int $roomTypeId = null): LengthAwarePaginator
    {
        return $this->repository->paginate($perPage, $roomTypeId);
    }

    public function paginateWithFilters(int $perPage = 15, ?string $search = null, ?int $roomTypeId = null, ?string $status = null): LengthAwarePaginator
    {
        return $this->repository->paginateWithFilters($perPage, $search, $roomTypeId, $status);
    }

    public function find(int $id): ?Room
    {
        return $this->repository->find($id);
    }

    /** @throws ValidationException */
    public function create(array $data): Room
    {
        $data['status'] = $data['status'] ?? Room::STATUS_AVAILABLE;
        $data['is_active'] = $data['is_active'] ?? true;
        return $this->repository->create($data);
    }

    /** @throws ValidationException */
    public function update(Room $room, array $data): Room
    {
        return $this->repository->update($room, $data);
    }

    /** Auto status: after checkout set room to cleaning. */
    public function setStatusCleaning(Room $room): Room
    {
        return $this->repository->update($room, ['status' => Room::STATUS_CLEANING]);
    }

    /** Mark room available after cleaning. */
    public function setStatusAvailable(Room $room): Room
    {
        return $this->repository->update($room, ['status' => Room::STATUS_AVAILABLE]);
    }

    public function delete(Room $room): bool
    {
        return $this->repository->delete($room);
    }
}
