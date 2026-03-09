<?php

namespace App\Repositories;

use App\Models\HousekeepingAssignment;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

class HousekeepingAssignmentRepository
{
    public function __construct(protected HousekeepingAssignment $model)
    {
    }

    public function forDate(Carbon $date, ?int $assignedTo = null, ?int $roomId = null): Collection
    {
        $q = $this->model->newQuery()->with(['room.roomType', 'room.latestBooking.guest', 'assignedTo'])
            ->where('date', $date->toDateString())
            ->orderByDesc('id');
        if ($assignedTo !== null) {
            $q->where('assigned_to', $assignedTo);
        }
        if ($roomId !== null) {
            $q->where('room_id', $roomId);
        }
        return $q->get();
    }

    public function find(int $id): ?HousekeepingAssignment
    {
        return $this->model->with(['room', 'assignedTo'])->find($id);
    }

    public function create(array $data): HousekeepingAssignment
    {
        return $this->model->create($data);
    }

    public function update(HousekeepingAssignment $a, array $data): HousekeepingAssignment
    {
        $a->update($data);
        return $a->fresh();
    }

    public function getOrCreateForRoom(int $roomId, Carbon $date, int $assignedTo): HousekeepingAssignment
    {
        return $this->model->firstOrCreate(
        ['room_id' => $roomId, 'date' => $date->toDateString()],
        ['assigned_to' => $assignedTo, 'status' => HousekeepingAssignment::STATUS_PENDING]
        );
    }
}
