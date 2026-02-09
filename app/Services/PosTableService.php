<?php

namespace App\Services;

use App\Models\PosTable;
use App\Repositories\PosTableRepository;
use Illuminate\Database\Eloquent\Collection;

class PosTableService
{
    public function __construct(protected PosTableRepository $repository) {}

    public function rules(bool $forUpdate = false): array
    {
        return [
            'outlet_id' => ['required', 'exists:outlets,id'],
            'name' => ['required', 'string', 'max:50'],
            'capacity' => ['nullable', 'integer', 'min:1'],
            'status' => ['nullable', 'in:available,occupied'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ];
    }

    public function byOutlet(int $outletId): Collection
    {
        return $this->repository->byOutlet($outletId);
    }

    public function find(int $id): ?PosTable
    {
        return $this->repository->find($id);
    }

    public function create(array $data): PosTable
    {
        $data['status'] = $data['status'] ?? PosTable::STATUS_AVAILABLE;
        $data['sort_order'] = $data['sort_order'] ?? 0;
        return $this->repository->create($data);
    }

    public function update(PosTable $table, array $data): PosTable
    {
        return $this->repository->update($table, $data);
    }

    /** Delete table. Fails if table has open (non-completed) orders. */
    public function delete(PosTable $table): void
    {
        $openOrders = $table->posOrders()->whereIn('status', ['pending', 'sent_to_kitchen'])->exists();
        if ($openOrders) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'table' => [__('Table has open orders. Complete or void them first.')],
            ]);
        }
        $this->repository->delete($table);
    }
}
