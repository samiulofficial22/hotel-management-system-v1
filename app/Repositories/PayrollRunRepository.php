<?php

namespace App\Repositories;

use App\Models\PayrollRun;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class PayrollRunRepository
{
    public function __construct(protected PayrollRun $model) {}

    public function find(int $id): ?PayrollRun
    {
        return $this->model->with(['items.employee'])->find($id);
    }

    public function paginate(int $perPage = 10): LengthAwarePaginator
    {
        return $this->model->newQuery()->orderByDesc('period_start')->paginate($perPage);
    }

    public function create(array $data): PayrollRun
    {
        return $this->model->create($data);
    }

    public function update(PayrollRun $run, array $data): PayrollRun
    {
        $run->update($data);
        return $run->fresh();
    }
}
