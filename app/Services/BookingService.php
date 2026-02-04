<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Room;
use App\Repositories\BookingRepository;
use App\Repositories\RoomRepository;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class BookingService
{
    public function __construct(
        protected BookingRepository $bookingRepository,
        protected RoomRepository $roomRepository
    ) {}

    public function rules(bool $forUpdate = false): array
    {
        $rules = [
            'guest_id' => ['required', 'exists:guests,id'],
            'room_id' => ['required', 'exists:rooms,id'],
            'check_in_date' => ['required', 'date', 'after_or_equal:today'],
            'check_out_date' => ['required', 'date', 'after:check_in_date'],
            'booking_type' => ['nullable', 'in:advance,walk_in'],
            'adults' => ['nullable', 'integer', 'min:1', 'max:20'],
            'children' => ['nullable', 'integer', 'min:0', 'max:20'],
            'room_rate' => ['required', 'numeric', 'min:0'],
            'special_requests' => ['nullable', 'string'],
            'internal_notes' => ['nullable', 'string'],
        ];
        if ($forUpdate) {
            $rules['check_in_date'][0] = 'required';
            $rules['check_in_date'][1] = 'date';
        }
        return $rules;
    }

    public function find(int $id): ?Booking
    {
        return $this->bookingRepository->find($id);
    }

    public function getForDateRange(Carbon $start, Carbon $end): Collection
    {
        return $this->bookingRepository->getForDateRange($start, $end);
    }

    public function paginate(int $perPage = 15, ?string $status = null): LengthAwarePaginator
    {
        return $this->bookingRepository->paginate($perPage, $status);
    }

    public function paginateWithSearch(int $perPage = 15, ?string $search = null, ?string $status = null): LengthAwarePaginator
    {
        return $this->bookingRepository->paginateWithSearch($perPage, $search, $status);
    }

    /** Overbooking protection: ensure room is not double-booked for the date range. */
    public function isRoomAvailable(int $roomId, Carbon $checkIn, Carbon $checkOut, ?int $excludeBookingId = null): bool
    {
        $overlapping = $this->bookingRepository->getOverlappingForRoom($roomId, $checkIn, $checkOut, $excludeBookingId);
        return $overlapping->isEmpty();
    }

    /** @throws ValidationException */
    public function create(array $data, ?int $createdBy = null): Booking
    {
        $checkIn = Carbon::parse($data['check_in_date']);
        $checkOut = Carbon::parse($data['check_out_date']);
        if (!$this->isRoomAvailable((int) $data['room_id'], $checkIn, $checkOut)) {
            throw ValidationException::withMessages(['room_id' => ['This room is already booked for the selected dates.']]);
        }

        $data['booking_number'] = $this->generateBookingNumber();
        $data['status'] = Booking::STATUS_CONFIRMED;
        $data['booking_type'] = $data['booking_type'] ?? Booking::TYPE_ADVANCE;
        $data['adults'] = $data['adults'] ?? 1;
        $data['children'] = $data['children'] ?? 0;
        $data['late_checkout_fee'] = $data['late_checkout_fee'] ?? 0;
        $data['created_by'] = $createdBy;

        return DB::transaction(function () use ($data) {
            $booking = $this->bookingRepository->create($data);
            $this->roomRepository->update($booking->room, ['status' => Room::STATUS_OCCUPIED]);
            return $booking;
        });
    }

    /** @throws ValidationException */
    public function update(Booking $booking, array $data): Booking
    {
        $checkIn = Carbon::parse($data['check_in_date'] ?? $booking->check_in_date);
        $checkOut = Carbon::parse($data['check_out_date'] ?? $booking->check_out_date);
        $roomId = (int) ($data['room_id'] ?? $booking->room_id);
        if (!$this->isRoomAvailable($roomId, $checkIn, $checkOut, $booking->id)) {
            throw ValidationException::withMessages(['room_id' => ['This room is already booked for the selected dates.']]);
        }

        return $this->bookingRepository->update($booking, $data);
    }

    /** Check-in: set status and timestamps. */
    public function checkIn(Booking $booking): Booking
    {
        return DB::transaction(function () use ($booking) {
            $this->bookingRepository->update($booking, [
                'status' => Booking::STATUS_CHECKED_IN,
                'checked_in_at' => now(),
            ]);
            $this->roomRepository->update($booking->room, ['status' => Room::STATUS_OCCUPIED]);
            return $booking->fresh();
        });
    }

    /** Check-out: set status, timestamps, optionally apply late checkout fee, set room to cleaning. */
    public function checkOut(Booking $booking, float $lateCheckoutFee = 0): Booking
    {
        return DB::transaction(function () use ($booking, $lateCheckoutFee) {
            $this->bookingRepository->update($booking, [
                'status' => Booking::STATUS_CHECKED_OUT,
                'checked_out_at' => now(),
                'late_checkout_fee' => $lateCheckoutFee,
            ]);
            $this->roomRepository->update($booking->room, ['status' => Room::STATUS_CLEANING]);
            return $booking->fresh();
        });
    }

    public function cancel(Booking $booking): Booking
    {
        return DB::transaction(function () use ($booking) {
            $this->bookingRepository->update($booking, ['status' => Booking::STATUS_CANCELLED]);
            if ($booking->room->status === Room::STATUS_OCCUPIED) {
                $this->roomRepository->update($booking->room, ['status' => Room::STATUS_AVAILABLE]);
            }
            return $booking->fresh();
        });
    }

    protected function generateBookingNumber(): string
    {
        $prefix = 'BK';
        $date = now()->format('Ymd');
        $last = Booking::withTrashed()->whereDate('created_at', today())->orderByDesc('id')->first();
        $seq = $last ? ((int) substr($last->booking_number, -4)) + 1 : 1;
        return $prefix . $date . str_pad((string) $seq, 4, '0', STR_PAD_LEFT);
    }
}
