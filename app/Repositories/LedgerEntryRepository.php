<?php

namespace App\Repositories;

use App\Models\LedgerEntry;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class LedgerEntryRepository
{
    public function __construct(protected LedgerEntry $model) {}

    public function byAccount(int $accountId, ?Carbon $from = null, ?Carbon $to = null): Collection
    {
        $q = $this->model->newQuery()->where('account_id', $accountId)->orderBy('entry_date')->orderBy('id');
        if ($from) {
            $q->where('entry_date', '>=', $from->toDateString());
        }
        if ($to) {
            $q->where('entry_date', '<=', $to->toDateString());
        }
        return $q->get();
    }

    public function paginate(int $perPage = 20, ?int $accountId = null): LengthAwarePaginator
    {
        $q = $this->model->newQuery()->with('account')->orderByDesc('entry_date')->orderByDesc('id');
        if ($accountId !== null) {
            $q->where('account_id', $accountId);
        }
        return $q->paginate($perPage);
    }

    public function create(array $data): LedgerEntry
    {
        return $this->model->create($data);
    }
}
