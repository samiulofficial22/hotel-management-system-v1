<?php

namespace App\Http\Controllers;

use App\Models\LaundryOrder;
use App\Models\Room;
use App\Models\LaundryItem;
use App\Services\LaundryService;
use App\Repositories\LaundryOrderRepository;
use Illuminate\Http\Request;

class LaundryOrderController extends Controller
{
    public function __construct(protected
        LaundryOrderRepository $repository, protected
        LaundryService $service
        )
    {
    }

    public function index(Request $request)
    {
        $orders = $this->repository->paginate(15, $request->status);
        return view('laundry.orders.index', compact('orders'));
    }

    public function create()
    {
        $rooms = Room::where('status', Room::STATUS_OCCUPIED)->get();
        $items = LaundryItem::where('is_active', true)->get();
        return view('laundry.orders.create', compact('rooms', 'items'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'items' => 'required|array|min:1',
            'items.*.laundry_item_id' => 'required|exists:laundry_items,id',
            'items.*.service_type' => 'required|in:wash,iron,dry_clean',
            'items.*.quantity' => 'required|integer|min:1',
            'payment_method' => 'nullable|string',
            'payment_status' => 'required|in:unpaid,paid',
            'status' => 'required|in:pending,processing,ready,delivered',
            'notes' => 'nullable|string'
        ]);

        $room = Room::find($request->room_id);
        $totalAmount = 0;
        $orderItems = [];

        foreach ($request->items as $val) {
            $lItem = LaundryItem::find($val['laundry_item_id']);
            $price = 0;
            if ($val['service_type'] === 'wash')
                $price = $lItem->wash_price;
            elseif ($val['service_type'] === 'iron')
                $price = $lItem->iron_price;
            elseif ($val['service_type'] === 'dry_clean')
                $price = $lItem->dry_clean_price;

            $subtotal = $price * $val['quantity'];
            $totalAmount += $subtotal;

            $orderItems[] = array_merge($val, ['unit_price' => $price]);
        }

        $orderData = [
            'room_id' => $request->room_id,
            'guest_id' => $room->latestBooking->guest_id ?? null,
            'total_amount' => $totalAmount,
            'status' => $request->status,
            'payment_status' => $request->payment_status,
            'payment_method' => $request->payment_method,
            'order_date' => now()->toDateString(),
            'order_time' => now()->toTimeString(),
            'notes' => $request->notes,
            'created_by' => auth()->id()
        ];

        $this->service->createOrder($orderData, $orderItems);

        return redirect()->route('laundry.orders.index')->with('success', 'Laundry order created');
    }

    public function show(LaundryOrder $order)
    {
        $order->load(['items.item', 'room', 'guest', 'createdBy']);
        return view('laundry.orders.show', compact('order'));
    }

    public function updateStatus(Request $request, LaundryOrder $order)
    {
        $request->validate(['status' => 'required|in:pending,processing,ready,delivered,cancelled']);
        $this->service->updateOrderStatus($order, $request->status);
        return back()->with('success', 'Status updated');
    }

    public function updatePayment(Request $request, LaundryOrder $order)
    {
        $request->validate(['payment_status' => 'required|in:unpaid,paid']);
        $this->service->updatePaymentStatus($order, $request->payment_status);
        return back()->with('success', 'Payment status updated');
    }

    public function destroy(LaundryOrder $order)
    {
        $this->repository->delete($order);
        return redirect()->route('laundry.orders.index')->with('success', 'Order deleted');
    }
}
