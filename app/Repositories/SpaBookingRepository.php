<?php

namespace App\Repositories;

use App\Models\SpaBooking;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class SpaBookingRepository
{
    public function paginate(int $perPage = 15, ?string $status = null): LengthAwarePaginator
    {
        $query = SpaBooking::with(['room', 'guest', 'service', 'createdBy']);
        if ($status) {
            $query->where('status', $status);
        }
        return $query->latest()->paginate($perPage);
    }

    public function find(int $id): ?SpaBooking
    {
        return SpaBooking::with(['service', 'room', 'guest'])->find($id);
    }

    public function create(array $data): SpaBooking
    {
        return SpaBooking::create($data);
    }

    public function update(SpaBooking $booking, array $data): bool
    {
        return $booking->update($data);
    }

    public function delete(SpaBooking $booking): bool
    {
        return $booking->delete();
    }
}
