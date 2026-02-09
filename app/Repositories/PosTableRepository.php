<?php

namespace App\Repositories;

use App\Models\PosTable;
use Illuminate\Database\Eloquent\Collection;

class PosTableRepository
{
    public function __construct(protected PosTable $model) {}

    public function byOutlet(int $outletId): Collection
    {
        return $this->model->newQuery()->where('outlet_id', $outletId)->orderBy('sort_order')->orderBy('name')->get();
    }

    public function find(int $id): ?PosTable
    {
        return $this->model->find($id);
    }

    public function create(array $data): PosTable
    {
        return $this->model->create($data);
    }

    public function update(PosTable $table, array $data): PosTable
    {
        $table->update($data);
        return $table->fresh();
    }

    public function delete(PosTable $table): void
    {
        $table->delete();
    }
}
