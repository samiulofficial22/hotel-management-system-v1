<?php

namespace App\Services;

use App\Models\LedgerEntry;
use App\Repositories\LedgerEntryRepository;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class LedgerEntryService
{
    public function __construct(protected LedgerEntryRepository $repository) {}

    public function byAccount(int $accountId, ?Carbon $from = null, ?Carbon $to = null): Collection
    {
        return $this->repository->byAccount($accountId, $from, $to);
    }

    public function balanceForAccount(int $accountId, ?Carbon $asOf = null): float
    {
        $q = LedgerEntry::where('account_id', $accountId);
        if ($asOf) {
            $q->where('entry_date', '<=', $asOf->toDateString());
        }
        $debit = (float) $q->clone()->sum('debit');
        $credit = (float) $q->clone()->sum('credit');
        $account = \App\Models\ChartOfAccount::find($accountId);
        $type = $account?->type ?? 'asset';
        return in_array($type, ['asset', 'expense'], true) ? $debit - $credit : $credit - $debit;
    }

    public function paginate(int $perPage = 20, ?int $accountId = null): LengthAwarePaginator
    {
        return $this->repository->paginate($perPage, $accountId);
    }

    public function create(array $data, ?int $userId = null): LedgerEntry
    {
        $data['created_by'] = $userId;
        return $this->repository->create($data);
    }

    public function createDoubleEntry(int $debitAccountId, int $creditAccountId, float $amount, string $date, ?string $description = null, ?string $refType = null, ?int $refId = null, ?int $userId = null): void
    {
        DB::transaction(function () use ($debitAccountId, $creditAccountId, $amount, $date, $description, $refType, $refId, $userId) {
            $this->repository->create([
                'entry_date' => $date,
                'account_id' => $debitAccountId,
                'debit' => $amount,
                'credit' => 0,
                'reference_type' => $refType,
                'reference_id' => $refId,
                'description' => $description,
                'created_by' => $userId,
            ]);
            $this->repository->create([
                'entry_date' => $date,
                'account_id' => $creditAccountId,
                'debit' => 0,
                'credit' => $amount,
                'reference_type' => $refType,
                'reference_id' => $refId,
                'description' => $description,
                'created_by' => $userId,
            ]);
        });
    }
}
