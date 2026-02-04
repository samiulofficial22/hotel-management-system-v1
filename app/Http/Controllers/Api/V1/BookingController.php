<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\ApiBaseController;
use App\Http\Resources\BookingResource;
use App\Models\Booking;
use App\Services\BookingService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Validation\ValidationException;

class BookingController extends ApiBaseController
{
    public function __construct(
        protected BookingService $service
    ) {}

    public function index(Request $request): AnonymousResourceCollection|JsonResponse
    {
        $status = $request->input('status');
        $start = $request->input('start');
        $end = $request->input('end');
        if ($start && $end) {
            $bookings = $this->service->getForDateRange(Carbon::parse($start), Carbon::parse($end));
            return $this->success(BookingResource::collection($bookings), 'Bookings');
        }
        $bookings = $this->service->paginate($request->integer('per_page', 15), $status);
        return BookingResource::collection($bookings)->additional(['success' => true, 'message' => 'Bookings']);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate($this->service->rules());
        try {
            $booking = $this->service->create($validated, $request->user()?->id);
            $booking->load(['guest', 'room.roomType']);
            return $this->success(new BookingResource($booking), 'Booking created', 201);
        } catch (ValidationException $e) {
            return $this->error('Validation failed', 422, $e->errors());
        }
    }

    public function show(Booking $booking): JsonResponse
    {
        $booking->load(['guest', 'room.roomType']);
        return $this->success(new BookingResource($booking), 'Booking');
    }

    public function update(Request $request, Booking $booking): JsonResponse
    {
        $validated = $request->validate($this->service->rules(true));
        try {
            $booking = $this->service->update($booking, $validated);
            $booking->load(['guest', 'room.roomType']);
            return $this->success(new BookingResource($booking), 'Booking updated');
        } catch (ValidationException $e) {
            return $this->error('Validation failed', 422, $e->errors());
        }
    }

    public function destroy(Booking $booking): JsonResponse
    {
        $this->service->cancel($booking);
        return $this->success(null, 'Booking cancelled');
    }
}
