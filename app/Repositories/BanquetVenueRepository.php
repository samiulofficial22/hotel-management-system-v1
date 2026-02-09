<?php

namespace App\Repositories;

use App\Models\BanquetVenue;
use Illuminate\Database\Eloquent\Collection;

class BanquetVenueRepository
{
    public function __construct(protected BanquetVenue $model) {}

    public function all(bool $activeOnly = true): Collection
    {
        $q = $this->model->newQuery()->orderByDesc('id');
        if ($activeOnly) {
            $q->where('is_active', true);
        }
        return $q->get();
    }

    public function find(int $id): ?BanquetVenue
    {
        return $this->model->find($id);
    }

    public function create(array $data): BanquetVenue
    {
        return $this->model->create($data);
    }

    public function update(BanquetVenue $venue, array $data): BanquetVenue
    {
        $venue->update($data);
        return $venue->fresh();
    }
}
