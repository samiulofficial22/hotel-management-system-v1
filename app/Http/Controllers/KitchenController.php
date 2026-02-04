<?php

namespace App\Http\Controllers;

use App\Models\PosOrderItem;
use App\Services\PosOrderService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class KitchenController extends Controller
{
    public function __construct(protected PosOrderService $orderService) {}

    public function index(): View
    {
        $orders = $this->orderService->kitchenPending();
        return view('kitchen.index', compact('orders'));
    }

    public function markItemReady(PosOrderItem $pos_order_item): RedirectResponse
    {
        $this->orderService->markItemReady($pos_order_item);
        return redirect()->route('kitchen.index')->with('success', __('Item marked ready.'));
    }
}
