<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\PosOrder;
use App\Models\PosOrderItem;
use App\Models\PosTable;
use App\Services\OutletService;
use App\Services\PosOrderService;
use App\Services\PosTableService;
use App\Services\MenuItemService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Validation\ValidationException;

class PosController extends Controller
{
    public function __construct(protected
        OutletService $outletService, protected
        PosOrderService $orderService, protected
        PosTableService $tableService, protected
        MenuItemService $menuItemService
        )
    {
    }

    public function index(): View
    {
        $outlets = $this->outletService->all(true);
        return view('pos.index', compact('outlets'));
    }

    /** Orders list & profit/loss report (completed orders by date range). */
    public function ordersList(Request $request): View
    {
        $from = $request->filled('from') ?Carbon::parse($request->from) : now()->startOfMonth();
        $to = $request->filled('to') ?Carbon::parse($request->to) : now()->endOfMonth();
        $outletId = $request->filled('outlet_id') ? (int)$request->outlet_id : null;
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

    /** POS reports: daily sales, by type, payment method, room charge vs instant. */
    public function reports(Request $request): View
    {
        $from = $request->filled('from') ?Carbon::parse($request->from) : now()->startOfMonth();
        $to = $request->filled('to') ?Carbon::parse($request->to) : now()->endOfMonth();
        $outletId = $request->filled('outlet_id') ? (int)$request->outlet_id : null;
        $report = $this->orderService->getCompletedOrdersReport($from, $to, $outletId);
        $orders = $report['orders'];
        $paidOrders = $orders->where('payment_status', PosOrder::PAYMENT_STATUS_PAID);
        $postedOrders = $orders->where('payment_status', PosOrder::PAYMENT_STATUS_POSTED_TO_ROOM);
        $instantTotal = $paidOrders->sum('total');
        $roomChargeTotal = $postedOrders->sum('total');
        $byType = $orders->groupBy('pos_type')->map(fn($o) => $o->sum('total'));
        $paymentMethodSummary = $orders->pluck('payments')->flatten()->groupBy('method')->map(fn($p) => round($p->sum('amount'), 2));
        $outlets = $this->outletService->all(true);
        return view('pos.reports', compact('orders', 'from', 'to', 'outletId', 'outlets', 'instantTotal', 'roomChargeTotal', 'byType', 'paymentMethodSummary'));
    }

    public function outlet(Request $request): View
    {
        $id = (int)$request->route('id');
        $outlet = $this->outletService->find($id);
        if (!$outlet) {
            abort(404);
        }
        $tables = $this->tableService->byOutlet($id);
        $openOrders = $this->orderService->openByOutlet($id);
        $menuItems = $this->menuItemService->byOutlet($id);
        return view('pos.outlet', compact('outlet', 'tables', 'openOrders', 'menuItems'));
    }

    public function storeTable(Request $request): RedirectResponse
    {
        $outletId = (int)$request->route('id');
        $request->validate([
            'name' => ['required', 'string', 'max:50'],
            'capacity' => ['nullable', 'integer', 'min:1'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);
        $this->tableService->create([
            'outlet_id' => $outletId,
            'name' => $request->input('name'),
            'capacity' => $request->input('capacity'),
            'sort_order' => $request->input('sort_order', 0),
        ]);
        return redirect()->route('pos.outlet', $outletId)->with('success', __('Table added.'));
    }

    public function destroyTable(PosTable $table): RedirectResponse
    {
        $outletId = $table->outlet_id;
        try {
            $this->tableService->delete($table);
            return redirect()->route('pos.outlet', $outletId)->with('success', __('Table deleted.'));
        }
        catch (ValidationException $e) {
            return redirect()->route('pos.outlet', $outletId)->withErrors($e->errors());
        }
    }

    public function createOrder(Request $request): RedirectResponse
    {
        $outletId = (int)$request->input('outlet_id');
        $tableId = $request->input('pos_table_id') ? (int)$request->input('pos_table_id') : null;
        $order = $this->orderService->create($outletId, $tableId, (int)auth()->id());
        return redirect()->route('pos.order', $order->id)->with('success', __('Order created.'));
    }

    public function order(Request $request): View
    {
        $order = $this->orderService->find((int)$request->route('order'));
        if (!$order) {
            abort(404);
        }
        $order->load(['items.menuItem', 'posTable', 'outlet', 'guest', 'booking.room']);
        $menuItems = $this->menuItemService->byOutlet($order->outlet_id);
        $activeBookings = Booking::with(['guest', 'room'])
            ->where('status', Booking::STATUS_CHECKED_IN)
            ->where('check_in_date', '<=', now()->toDateString())
            ->where('check_out_date', '>=', now()->toDateString())
            ->orderBy('booking_number')
            ->get();
        return view('pos.order', compact('order', 'menuItems', 'activeBookings'));
    }

    public function addItem(Request $request): RedirectResponse
    {
        $orderId = (int)$request->route('order');
        $order = $this->orderService->find($orderId);
        if (!$order) {
            abort(404);
        }
        $request->validate(['menu_item_id' => 'required|exists:menu_items,id', 'quantity' => 'nullable|integer|min:1']);
        $this->orderService->addItem($order, (int)$request->menu_item_id, (int)($request->quantity ?? 1), $request->notes);
        return redirect()->route('pos.order', $order->id)->with('success', __('Item added.'));
    }

    public function removeItem(Request $request): RedirectResponse
    {
        $orderId = (int)$request->route('order');
        $order = $this->orderService->find($orderId);
        if (!$order) {
            abort(404);
        }
        $this->orderService->removeItem($order, (int)$request->route('item'));
        return redirect()->route('pos.order', $order->id)->with('success', __('Item removed.'));
    }

    public function sendToKitchen(Request $request): RedirectResponse
    {
        $order = $this->orderService->find((int)$request->route('order'));
        if (!$order) {
            abort(404);
        }
        $this->orderService->sendToKitchen($order);
        return redirect()->route('pos.order', $order->id)->with('success', __('Order sent to kitchen.'));
    }

    public function completeOrder(Request $request): RedirectResponse
    {
        $order = $this->orderService->find((int)$request->route('order'));
        if (!$order) {
            abort(404);
        }
        $this->orderService->completeOrder($order);
        return redirect()->route('pos.order', $order->id)->with('success', __('Order completed. Complete payment or post to room.'));
    }

    public function payOrder(Request $request): RedirectResponse
    {
        $order = $this->orderService->find((int)$request->route('order'));
        if (!$order) {
            abort(404);
        }
        $payments = $request->input('payments', []);
        if (empty($payments)) {
            $payments = [['amount' => $order->total, 'method' => $request->input('method', 'cash'), 'reference' => $request->input('reference')]];
        }
        try {
            $this->orderService->payOrder($order, $payments);
            return redirect()->route('pos.outlet', $order->outlet_id)->with('success', __('Payment recorded.'));
        }
        catch (ValidationException $e) {
            return redirect()->route('pos.order', $order->id)->withErrors($e->errors())->withInput();
        }
    }

    public function postOrderToRoom(Request $request): RedirectResponse
    {
        $order = $this->orderService->find((int)$request->route('order'));
        if (!$order) {
            abort(404);
        }
        $bookingId = $request->input('booking_id') ? (int)$request->input('booking_id') : null;
        if ($bookingId && !$order->booking_id) {
            $booking = Booking::find($bookingId);
            if ($booking) {
                $order->update(['booking_id' => $booking->id, 'guest_id' => $booking->guest_id]);
            }
        }
        try {
            $this->orderService->postOrderToRoom($order);
            return redirect()->route('pos.outlet', $order->outlet_id)->with('success', __('Charge posted to room. Will appear on guest invoice at checkout.'));
        }
        catch (ValidationException $e) {
            return redirect()->route('pos.order', $order->id)->withErrors($e->errors())->withInput();
        }
    }

    public function voidOrder(Request $request): RedirectResponse
    {
        $order = $this->orderService->find((int)$request->route('order'));
        if (!$order) {
            abort(404);
        }
        try {
            $this->orderService->voidOrder($order);
            return redirect()->route('pos.outlet', $order->outlet_id)->with('success', __('Order voided.'));
        }
        catch (ValidationException $e) {
            return redirect()->route('pos.order', $order->id)->withErrors($e->errors());
        }
    }
}
