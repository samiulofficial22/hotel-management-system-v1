<?php

namespace App\Http\Controllers;

use App\Models\MarketingCampaign;
use App\Services\MarketingCampaignService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Validation\ValidationException;

class MarketingCampaignController extends Controller
{
    public function __construct(protected MarketingCampaignService $service) {}

    public function index(): View
    {
        $campaigns = $this->service->paginate(15);
        return view('marketing.index', compact('campaigns'));
    }

    public function create(): View
    {
        return view('marketing.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate($this->service->rules());
        try {
            $campaign = $this->service->create($validated, auth()->id());
            return redirect()->route('marketing.show', $campaign)->with('success', __('Campaign created.'));
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        }
    }

    public function show(MarketingCampaign $marketing_campaign): View
    {
        $campaign = $this->service->find($marketing_campaign->id);
        return view('marketing.show', compact('campaign'));
    }

    public function edit(MarketingCampaign $marketing_campaign): View
    {
        return view('marketing.edit', ['campaign' => $marketing_campaign]);
    }

    public function update(Request $request, MarketingCampaign $marketing_campaign): RedirectResponse
    {
        $validated = $request->validate($this->service->rules(true));
        try {
            $this->service->update($marketing_campaign, $validated);
            return redirect()->route('marketing.show', $marketing_campaign)->with('success', __('Campaign updated.'));
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        }
    }

    public function addRecipients(MarketingCampaign $marketing_campaign, Request $request): RedirectResponse
    {
        $guestIds = $request->input('guest_ids', []);
        $count = $this->service->addRecipientsFromGuests($marketing_campaign, is_array($guestIds) && !empty($guestIds) ? $guestIds : null);
        return redirect()->route('marketing.show', $marketing_campaign)->with('success', __(':count recipients added.', ['count' => $count]));
    }
}
