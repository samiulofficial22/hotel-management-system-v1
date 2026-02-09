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

    /** Sum of (debit - credit) for expense accounts in date range. */
    public function totalExpense(Carbon $from, Carbon $to): float
    {
        $accountIds = \App\Models\ChartOfAccount::where('type', 'expense')->where('is_active', true)->pluck('id');
        $debit = (float) LedgerEntry::whereIn('account_id', $accountIds)->whereBetween('entry_date', [$from->toDateString(), $to->toDateString()])->sum('debit');
        $credit = (float) LedgerEntry::whereIn('account_id', $accountIds)->whereBetween('entry_date', [$from->toDateString(), $to->toDateString()])->sum('credit');
        return $debit - $credit;
    }

    /** Sum of (credit - debit) for revenue accounts in date range. */
    public function totalRevenue(Carbon $from, Carbon $to): float
    {
        $accountIds = \App\Models\ChartOfAccount::where('type', 'revenue')->where('is_active', true)->pluck('id');
        $debit = (float) LedgerEntry::whereIn('account_id', $accountIds)->whereBetween('entry_date', [$from->toDateString(), $to->toDateString()])->sum('debit');
        $credit = (float) LedgerEntry::whereIn('account_id', $accountIds)->whereBetween('entry_date', [$from->toDateString(), $to->toDateString()])->sum('credit');
        return $credit - $debit;
    }

    /** Expense breakdown by account (code, name, amount) for date range. */
    public function expenseByAccount(Carbon $from, Carbon $to): array
    {
        $accounts = \App\Models\ChartOfAccount::where('type', 'expense')->where('is_active', true)->orderBy('sort_order')->get();
        $result = [];
        foreach ($accounts as $acc) {
            $debit = (float) LedgerEntry::where('account_id', $acc->id)->whereBetween('entry_date', [$from->toDateString(), $to->toDateString()])->sum('debit');
            $credit = (float) LedgerEntry::where('account_id', $acc->id)->whereBetween('entry_date', [$from->toDateString(), $to->toDateString()])->sum('credit');
            $amount = $debit - $credit;
            if ($amount != 0) {
                $result[] = ['code' => $acc->code, 'name' => $acc->name, 'amount' => $amount];
            }
        }
        return $result;
    }

    /** Daily cash movement: date => net (debit - credit) for CASH account. */
    public function dailyCashSummary(Carbon $from, Carbon $to): array
    {
        $cashAccount = \App\Models\ChartOfAccount::where('code', 'CASH')->where('is_active', true)->first();
        if (! $cashAccount) {
            return [];
        }
        $entries = LedgerEntry::where('account_id', $cashAccount->id)
            ->whereBetween('entry_date', [$from->toDateString(), $to->toDateString()])
            ->selectRaw('entry_date, SUM(debit) as debit, SUM(credit) as credit')
            ->groupBy('entry_date')
            ->orderBy('entry_date')
            ->get();
        return $entries->mapWithKeys(function ($row) {
            $net = (float) $row->debit - (float) $row->credit;
            $dateKey = $row->entry_date instanceof Carbon
                ? $row->entry_date->toDateString()
                : Carbon::parse($row->entry_date)->toDateString();
            return [$dateKey => $net];
        })->all();
    }
}
