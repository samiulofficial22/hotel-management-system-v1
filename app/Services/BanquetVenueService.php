<?php

namespace App\Services;

use App\Models\BanquetVenue;
use App\Repositories\BanquetVenueRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Validation\ValidationException;

class BanquetVenueService
{
    public function __construct(protected BanquetVenueRepository $repository)
    {
    }

    public function rules(bool $forUpdate = false): array
    {
        return [
            'name' => ['required', 'string', 'max:100'],
            'location' => ['nullable', 'string', 'max:255'],
            'capacity' => ['required', 'integer', 'min:1'],
            'hourly_rate' => ['nullable', 'numeric', 'min:0'],
            'fixed_rate' => ['nullable', 'numeric', 'min:0'],
            'description' => ['nullable', 'string'],
            'is_active' => ['boolean'],
        ];
    }

    public function all(bool $activeOnly = true): Collection
    {
        return $this->repository->all($activeOnly);
    }

    public function find(int $id): ?BanquetVenue
    {
        return $this->repository->find($id);
    }

    /** @throws ValidationException */
    public function create(array $data): BanquetVenue
    {
        $data['is_active'] = $data['is_active'] ?? true;
        return $this->repository->create($data);
    }

    /** @throws ValidationException */
    public function update(BanquetVenue $venue, array $data): BanquetVenue
    {
        return $this->repository->update($venue, $data);
    }

    /** @throws ValidationException When venue has bookings. */
    public function delete(BanquetVenue $venue): void
    {
        if ($venue->banquetBookings()->exists()) {
            throw ValidationException::withMessages([
                'venue' => [__('Cannot delete venue that has bookings.')],
            ]);
        }
        $venue->delete();
    }
}
