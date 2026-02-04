<?php

namespace App\Services;

use App\Models\ChartOfAccount;
use App\Repositories\ChartOfAccountRepository;
use Illuminate\Database\Eloquent\Collection;

class ChartOfAccountService
{
    public function __construct(protected ChartOfAccountRepository $repository) {}

    public function rules(?int $accountId = null): array
    {
        $codeRule = $accountId ? 'required|string|max:20|unique:chart_of_accounts,code,' . $accountId : 'required|string|max:20|unique:chart_of_accounts,code';
        return [
            'code' => [$codeRule],
            'name' => ['required', 'string', 'max:150'],
            'type' => ['required', 'in:asset,liability,equity,revenue,expense'],
            'parent_id' => ['nullable', 'exists:chart_of_accounts,id'],
            'is_active' => ['boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ];
    }

    public function all(bool $activeOnly = true): Collection
    {
        return $this->repository->all($activeOnly);
    }

    public function find(int $id): ?ChartOfAccount
    {
        return $this->repository->find($id);
    }

    public function create(array $data): ChartOfAccount
    {
        $data['is_active'] = $data['is_active'] ?? true;
        $data['sort_order'] = $data['sort_order'] ?? 0;
        return $this->repository->create($data);
    }

    public function update(ChartOfAccount $account, array $data): ChartOfAccount
    {
        return $this->repository->update($account, $data);
    }
}
