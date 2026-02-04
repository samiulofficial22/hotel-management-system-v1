<?php

namespace App\Services;

use App\Models\BanquetBooking;
use App\Repositories\BanquetBookingRepository;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Validation\ValidationException;

class BanquetBookingService
{
    public function __construct(protected BanquetBookingRepository $repository) {}

    public function rules(bool $forUpdate = false): array
    {
        return [
            'banquet_venue_id' => ['required', 'exists:banquet_venues,id'],
            'event_name' => ['required', 'string', 'max:150'],
            'event_date' => ['required', 'date'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
            'guest_count' => ['required', 'integer', 'min:1'],
            'package_name' => ['nullable', 'string', 'max:100'],
            'total_amount' => ['nullable', 'numeric', 'min:0'],
            'status' => ['nullable', 'in:pending,confirmed,cancelled,completed'],
            'contact_name' => ['required', 'string', 'max:100'],
            'contact_phone' => ['required', 'string', 'max:30'],
            'contact_email' => ['nullable', 'email'],
            'notes' => ['nullable', 'string'],
        ];
    }

    public function find(int $id): ?BanquetBooking
    {
        return $this->repository->find($id);
    }

    public function paginate(int $perPage = 15, ?string $status = null): LengthAwarePaginator
    {
        return $this->repository->paginate($perPage, $status);
    }

    public function paginateWithSearch(int $perPage = 15, ?string $search = null, ?string $status = null): LengthAwarePaginator
    {
        return $this->repository->paginateWithSearch($perPage, $search, $status);
    }

    public function byVenueAndDate(int $venueId, Carbon $date): Collection
    {
        return $this->repository->byVenueAndDate($venueId, $date);
    }

    /** @throws ValidationException */
    public function create(array $data, ?int $userId = null): BanquetBooking
    {
        $data['created_by'] = $userId;
        $data['status'] = $data['status'] ?? BanquetBooking::STATUS_PENDING;
        $data['total_amount'] = $data['total_amount'] ?? 0;
        return $this->repository->create($data);
    }

    /** @throws ValidationException */
    public function update(BanquetBooking $booking, array $data): BanquetBooking
    {
        return $this->repository->update($booking, $data);
    }
}
