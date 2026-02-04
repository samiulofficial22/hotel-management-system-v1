<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\ApiBaseController;
use App\Http\Resources\RoomTypeResource;
use App\Models\RoomType;
use App\Services\RoomTypeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Validation\ValidationException;

class RoomTypeController extends ApiBaseController
{
    public function __construct(
        protected RoomTypeService $service
    ) {}

    public function index(Request $request): AnonymousResourceCollection|JsonResponse
    {
        $activeOnly = $request->boolean('active_only', false);
        $items = $activeOnly ? $this->service->all(true) : $this->service->paginate($request->integer('per_page', 15));
        if ($activeOnly) {
            return $this->success(RoomTypeResource::collection($items), 'Room types');
        }
        return RoomTypeResource::collection($items)->additional(['success' => true, 'message' => 'Room types']);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate($this->service->rules());
        try {
            $roomType = $this->service->create($validated);
            return $this->success(new RoomTypeResource($roomType), 'Room type created', 201);
        } catch (ValidationException $e) {
            return $this->error('Validation failed', 422, $e->errors());
        }
    }

    public function show(RoomType $roomType): JsonResponse
    {
        return $this->success(new RoomTypeResource($roomType), 'Room type');
    }

    public function update(Request $request, RoomType $roomType): JsonResponse
    {
        $rules = $this->service->rules(true);
        $rules['slug'] = ['nullable', 'string', 'max:100', 'unique:room_types,slug,' . $roomType->id];
        $validated = $request->validate($rules);
        try {
            $roomType = $this->service->update($roomType, $validated);
            return $this->success(new RoomTypeResource($roomType), 'Room type updated');
        } catch (ValidationException $e) {
            return $this->error('Validation failed', 422, $e->errors());
        }
    }

    public function destroy(RoomType $roomType): JsonResponse
    {
        $this->service->delete($roomType);
        return $this->success(null, 'Room type deleted');
    }
}
