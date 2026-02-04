<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\ApiBaseController;
use App\Http\Resources\GuestResource;
use App\Models\Guest;
use App\Services\GuestService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class GuestController extends ApiBaseController
{
    public function __construct(
        protected GuestService $service
    ) {}

    public function index(Request $request): AnonymousResourceCollection|JsonResponse
    {
        if ($request->filled('q')) {
            $guests = $this->service->search($request->input('q'));
            return $this->success(GuestResource::collection($guests), 'Guests');
        }
        $guests = $this->service->paginate($request->integer('per_page', 15));
        return GuestResource::collection($guests)->additional(['success' => true, 'message' => 'Guests']);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate($this->service->rules());
        $guest = $this->service->create($validated);
        return $this->success(new GuestResource($guest), 'Guest created', 201);
    }

    public function show(Guest $guest): JsonResponse
    {
        return $this->success(new GuestResource($guest), 'Guest');
    }

    public function update(Request $request, Guest $guest): JsonResponse
    {
        $validated = $request->validate($this->service->rules());
        $guest = $this->service->update($guest, $validated);
        return $this->success(new GuestResource($guest), 'Guest updated');
    }

    public function destroy(Guest $guest): JsonResponse
    {
        $this->service->delete($guest);
        return $this->success(null, 'Guest deleted');
    }
}
