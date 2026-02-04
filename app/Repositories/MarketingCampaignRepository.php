<?php

namespace App\Repositories;

use App\Models\MarketingCampaign;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class MarketingCampaignRepository
{
    public function __construct(protected MarketingCampaign $model) {}

    public function all(): Collection
    {
        return $this->model->newQuery()->orderByDesc('created_at')->get();
    }

    public function find(int $id): ?MarketingCampaign
    {
        return $this->model->with('recipients.guest')->find($id);
    }

    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->newQuery()->orderByDesc('created_at')->paginate($perPage);
    }

    public function create(array $data): MarketingCampaign
    {
        return $this->model->create($data);
    }

    public function update(MarketingCampaign $campaign, array $data): MarketingCampaign
    {
        $campaign->update($data);
        return $campaign->fresh();
    }
}
