<?php

namespace App\Http\Controllers;

use App\Models\PosOrder;
use App\Models\PosOrderItem;
use App\Services\OutletService;
use App\Services\PosOrderService;
use App\Services\PosTableService;
use App\Services\MenuItemService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PosController extends Controller
{
    public function __construct(
        protected OutletService $outletService,
        protected PosOrderService $orderService,
        protected PosTableService $tableService,
        protected MenuItemService $menuItemService
    ) {}

    public function index(): View
    {
        $outlets = $this->outletService->all(true);
        return view('pos.index', compact('outlets'));
    }

    /** Orders list & profit/loss report (completed orders by date range). */
    public function ordersList(Request $request): View
    {
        $from = $request->filled('from') ? Carbon::parse($request->from) : now()->startOfMonth();
        $to = $request->filled('to') ? Carbon::parse($request->to) : now()->endOfMonth();
        $outletId = $request->filled('outlet_id') ? (int) $request->outlet_id : null;
        $report = $this->orderService->getCompletedOrdersReport($from, $to, $outletId);
        $outlets = $this->outletService->all(true);
        return view('pos.orders-list', [
            'orders' => $report['orders'],
            'revenue' => $report['revenue'],
            'cost' => $report['cost'],
            'profit' => $report['profit'],
            'from' => $from,
            'to' => $to,
            'outlets' => $outlets,
            'outletId' => $outletId,
        ]);
    }

    public function outlet(Request $request): View
    {
        $id = (int) $request->route('id');
        $outlet = $this->outletService->find($id);
        if (!$outlet) {
            abort(404);
        }
        $tables = $this->tableService->byOutlet($id);
        $openOrders = $this->orderService->openByOutlet($id);
        $menuItems = $this->menuItemService->byOutlet($id);
        return view('pos.outlet', compact('outlet', 'tables', 'openOrders', 'menuItems'));
    }

    public function createOrder(Request $request): RedirectResponse
    {
        $outletId = (int) $request->input('outlet_id');
        $tableId = $request->input('pos_table_id') ? (int) $request->input('pos_table_id') : null;
        $order = $this->orderService->create($outletId, $tableId, (int) auth()->id());
        return redirect()->route('pos.order', $order->id)->with('success', __('Order created.'));
    }

    public function order(Request $request): View
    {
        $order = $this->orderService->find((int) $request->route('order'));
        if (!$order) {
            abort(404);
        }
        $order->load(['items.menuItem', 'posTable', 'outlet']);
        $menuItems = $this->menuItemService->byOutlet($order->outlet_id);
        return view('pos.order', compact('order', 'menuItems'));
    }

    public function addItem(Request $request): RedirectResponse
    {
        $orderId = (int) $request->route('order');
        $order = $this->orderService->find($orderId);
        if (!$order) {
            abort(404);
        }
        $request->validate(['menu_item_id' => 'required|exists:menu_items,id', 'quantity' => 'nullable|integer|min:1']);
        $this->orderService->addItem($order, (int) $request->menu_item_id, (int) ($request->quantity ?? 1), $request->notes);
        return redirect()->route('pos.order', $order->id)->with('success', __('Item added.'));
    }

    public function removeItem(Request $request): RedirectResponse
    {
        $orderId = (int) $request->route('order');
        $order = $this->orderService->find($orderId);
        if (!$order) {
            abort(404);
        }
        $this->orderService->removeItem($order, (int) $request->route('item'));
        return redirect()->route('pos.order', $order->id)->with('success', __('Item removed.'));
    }

    public function sendToKitchen(Request $request): RedirectResponse
    {
        $order = $this->orderService->find((int) $request->route('order'));
        if (!$order) {
            abort(404);
        }
        $this->orderService->sendToKitchen($order);
        return redirect()->route('pos.order', $order->id)->with('success', __('Order sent to kitchen.'));
    }

    public function completeOrder(Request $request): RedirectResponse
    {
        $order = $this->orderService->find((int) $request->route('order'));
        if (!$order) {
            abort(404);
        }
        $this->orderService->completeOrder($order);
        return redirect()->route('pos.outlet', $order->outlet_id)->with('success', __('Order completed.'));
    }
}
