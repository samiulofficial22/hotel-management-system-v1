<?php

namespace App\Services;

use App\Models\MarketingCampaign;
use App\Models\CampaignRecipient;
use App\Repositories\MarketingCampaignRepository;
use App\Repositories\GuestRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class MarketingCampaignService
{
    public function __construct(
        protected MarketingCampaignRepository $repository,
        protected GuestRepository $guestRepository
    ) {}

    public function rules(bool $forUpdate = false): array
    {
        return [
            'name' => ['required', 'string', 'max:150'],
            'type' => ['nullable', 'in:email,sms,both'],
            'start_date' => ['required', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'status' => ['nullable', 'in:draft,active,completed'],
            'subject' => ['nullable', 'string'],
            'body' => ['nullable', 'string'],
        ];
    }

    public function find(int $id): ?MarketingCampaign
    {
        return $this->repository->find($id);
    }

    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->paginate($perPage);
    }

    public function create(array $data, ?int $userId = null): MarketingCampaign
    {
        $data['created_by'] = $userId;
        $data['type'] = $data['type'] ?? MarketingCampaign::TYPE_EMAIL;
        $data['status'] = $data['status'] ?? MarketingCampaign::STATUS_DRAFT;
        return $this->repository->create($data);
    }

    public function update(MarketingCampaign $campaign, array $data): MarketingCampaign
    {
        return $this->repository->update($campaign, $data);
    }

    public function addRecipientsFromGuests(MarketingCampaign $campaign, ?array $guestIds = null): int
    {
        $guests = $guestIds !== null
            ? $this->guestRepository->getByIds($guestIds)
            : $this->guestRepository->all();
        $count = 0;
        foreach ($guests as $guest) {
            if ($guest->email && CampaignRecipient::where('marketing_campaign_id', $campaign->id)->where('guest_id', $guest->id)->doesntExist()) {
                CampaignRecipient::create(['marketing_campaign_id' => $campaign->id, 'guest_id' => $guest->id, 'status' => 'pending']);
                $count++;
            }
        }
        return $count;
    }
}
