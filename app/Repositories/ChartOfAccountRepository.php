<?php

namespace App\Repositories;

use App\Models\ChartOfAccount;
use Illuminate\Database\Eloquent\Collection;

class ChartOfAccountRepository
{
    public function __construct(protected ChartOfAccount $model)
    {
    }

    public function all(bool $activeOnly = true): Collection
    {
        $q = $this->model->newQuery()->orderBy('sort_order')->orderBy('code');
        if ($activeOnly) {
            $q->where('is_active', true);
        }
        return $q->get();
    }

    public function find(int $id): ?ChartOfAccount
    {
        return $this->model->find($id);
    }

    public function create(array $data): ChartOfAccount
    {
        return $this->model->create($data);
    }

    public function update(ChartOfAccount $account, array $data): ChartOfAccount
    {
        $account->update($data);
        return $account->fresh();
    }

    public function delete(ChartOfAccount $account): bool
    {
        return (bool)$account->delete();
    }
}
