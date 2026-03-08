<?php namespace App\Services;

use App\Models\HousekeepingAssignment;
use App\Models\Room;
use App\Repositories\HousekeepingAssignmentRepository;
use App\Repositories\RoomRepository;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

class HousekeepingService
{
    public function __construct(
        protected HousekeepingAssignmentRepository $assignmentRepository,
        protected RoomRepository $roomRepository
    ) {}

    public function assignmentsForDate(Carbon $date, ?int $userId = null, ?int $roomId = null): Collection
    {
        return $this->assignmentRepository->forDate($date, $userId, $roomId);
    }

    public function allRooms(): Collection
    {
        return $this->roomRepository->all(false);
    }

    public function findAssignment(int $id): ?HousekeepingAssignment
    {
        return $this->assignmentRepository->find($id);
    }

    public function assignRoom(int $roomId, Carbon $date, int $assignedTo): HousekeepingAssignment
    {
        return $this->assignmentRepository->getOrCreateForRoom($roomId, $date, $assignedTo);
    }

    public function startAssignment(HousekeepingAssignment $a): HousekeepingAssignment
    {
        $a->update(['status' => HousekeepingAssignment::STATUS_IN_PROGRESS]);
        $a->room?->update(['status' => Room::STATUS_CLEANING]);
        return $a->fresh();
    }

    public function completeAssignment(HousekeepingAssignment $a, ?string $notes = null): HousekeepingAssignment
    {
        $a->update(['status' => HousekeepingAssignment::STATUS_COMPLETED, 'completed_at' => now(), 'notes' => $notes ?? $a->notes]);
        $a->room?->update(['status' => Room::STATUS_AVAILABLE]);
        return $a->fresh();
    }

    public function reassign(HousekeepingAssignment $a, int $userId): HousekeepingAssignment
    {
        $a->update(['assigned_to' => $userId]);
        return $a->fresh();
    }

    public function getRoomsNeedingCleaning(Carbon $date): Collection
    {
        $assignedRoomIds = HousekeepingAssignment::where('date', $date->toDateString())->pluck('room_id');
        
        return Room::whereIn('status', [Room::STATUS_CLEANING, Room::STATUS_OCCUPIED])
            ->whereNotIn('id', $assignedRoomIds)
            ->with(['roomType', 'latestBooking.guest'])
            ->get();
    }

    public function getUnassignedRooms(Carbon $date): Collection
    {
        $assignedRoomIds = HousekeepingAssignment::where('date', $date->toDateString())->pluck('room_id');
        return Room::whereNotIn('id', $assignedRoomIds)
            ->with(['roomType'])
            ->orderBy('number')
            ->get();
    }
}
