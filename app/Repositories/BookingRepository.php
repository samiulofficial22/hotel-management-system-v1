<?php

namespace App\Repositories;

use App\Models\Booking;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class BookingRepository
{
    public function __construct(
        protected Booking $model
    ) {}

    public function find(int $id): ?Booking
    {
        return $this->model->with(['guest', 'room.roomType', 'createdBy'])->find($id);
    }

    public function findByNumber(string $bookingNumber): ?Booking
    {
        return $this->model->where('booking_number', $bookingNumber)->with(['guest', 'room'])->first();
    }

    /** Bookings overlapping with given date range for a room (for overbooking check). */
    public function getOverlappingForRoom(int $roomId, Carbon $checkIn, Carbon $checkOut, ?int $excludeBookingId = null): Collection
    {
        $q = $this->model->newQuery()
            ->where('room_id', $roomId)
            ->whereNotIn('status', [Booking::STATUS_CANCELLED, Booking::STATUS_NO_SHOW])
            ->where('check_in_date', '<', $checkOut->toDateString())
            ->where('check_out_date', '>', $checkIn->toDateString());
        if ($excludeBookingId !== null) {
            $q->where('id', '!=', $excludeBookingId);
        }
        return $q->get();
    }

    public function getForDateRange(Carbon $start, Carbon $end): Collection
    {
        return $this->model->newQuery()
            ->with(['guest', 'room.roomType'])
            ->where('check_in_date', '<=', $end->toDateString())
            ->where('check_out_date', '>=', $start->toDateString())
            ->whereNotIn('status', [Booking::STATUS_CANCELLED])
            ->orderBy('check_in_date')
            ->orderBy('room_id')
            ->get();
    }

    public function paginate(int $perPage = 15, ?string $status = null): LengthAwarePaginator
    {
        $q = $this->model->newQuery()->with(['guest', 'room.roomType'])->orderByDesc('created_at');
        if ($status !== null) {
            $q->where('status', $status);
        }
        return $q->paginate($perPage);
    }

    /** Paginate with optional search (booking number, guest name, room number) and status filter. */
    public function paginateWithSearch(int $perPage = 15, ?string $search = null, ?string $status = null): LengthAwarePaginator
    {
        $q = $this->model->newQuery()->with(['guest', 'room.roomType'])->orderByDesc('created_at');
        if ($search !== null && trim($search) !== '') {
            $term = '%' . trim($search) . '%';
            $q->where(function ($query) use ($term) {
                $query->where('booking_number', 'like', $term)
                    ->orWhereHas('guest', fn ($q) => $q->where('first_name', 'like', $term)->orWhere('last_name', 'like', $term))
                    ->orWhereHas('room', fn ($q) => $q->where('number', 'like', $term));
            });
        }
        if ($status !== null && $status !== '') {
            $q->where('status', $status);
        }
        return $q->paginate($perPage)->withQueryString();
    }

    public function create(array $data): Booking
    {
        return $this->model->create($data);
    }

    public function update(Booking $booking, array $data): Booking
    {
        $booking->update($data);
        return $booking->fresh();
    }

    public function delete(Booking $booking): bool
    {
        return $booking->delete();
    }
}
