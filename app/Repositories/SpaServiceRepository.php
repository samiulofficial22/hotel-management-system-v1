<?php

namespace App\Repositories;

use App\Models\SpaService;
use Illuminate\Database\Eloquent\Collection;

class SpaServiceRepository
{
    public function all(): Collection
    {
        return SpaService::all();
    }

    public function find(int $id): ?SpaService
    {
        return SpaService::find($id);
    }

    public function create(array $data): SpaService
    {
        return SpaService::create($data);
    }

    public function update(SpaService $service, array $data): bool
    {
        return $service->update($data);
    }

    public function delete(SpaService $service): bool
    {
        return $service->delete();
    }
}
