<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\ApiBaseController;
use App\Http\Resources\RoomResource;
use App\Models\Room;
use App\Services\RoomService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Validation\ValidationException;

class RoomController extends ApiBaseController
{
    public function __construct(
        protected RoomService $service
    ) {}

    public function index(Request $request): AnonymousResourceCollection|JsonResponse
    {
        $roomTypeId = $request->filled('room_type_id') ? (int) $request->room_type_id : null;
        $status = $request->input('status');
        if ($status === 'available') {
            $items = $this->service->getAvailable();
            return $this->success(RoomResource::collection($items), 'Available rooms');
        }
        $rooms = $this->service->paginate($request->integer('per_page', 15), $roomTypeId);
        return RoomResource::collection($rooms)->additional(['success' => true, 'message' => 'Rooms']);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate($this->service->rules());
        try {
            $room = $this->service->create($validated);
            return $this->success(new RoomResource($room->load('roomType')), 'Room created', 201);
        } catch (ValidationException $e) {
            return $this->error('Validation failed', 422, $e->errors());
        }
    }

    public function show(Room $room): JsonResponse
    {
        $room->load('roomType');
        return $this->success(new RoomResource($room), 'Room');
    }

    public function update(Request $request, Room $room): JsonResponse
    {
        $rules = $this->service->rules(true);
        $rules['number'] = ['required', 'string', 'max:20', 'unique:rooms,number,' . $room->id];
        $validated = $request->validate($rules);
        try {
            $room = $this->service->update($room, $validated);
            return $this->success(new RoomResource($room->load('roomType')), 'Room updated');
        } catch (ValidationException $e) {
            return $this->error('Validation failed', 422, $e->errors());
        }
    }

    public function destroy(Room $room): JsonResponse
    {
        $this->service->delete($room);
        return $this->success(null, 'Room deleted');
    }
}
