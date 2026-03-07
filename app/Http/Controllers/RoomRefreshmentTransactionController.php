<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Room;
use App\Repositories\RoomRefreshmentRepository;
use App\Services\RoomRefreshmentService;
use Illuminate\Http\Request;

class RoomRefreshmentTransactionController extends Controller
{
    public function __construct(protected
        RoomRefreshmentRepository $repository, protected
        RoomRefreshmentService $service
        )
    {
    }

    public function index(Request $request)
    {
        $filters = $request->only(['room_id', 'item_id', 'date']);
        $transactions = $this->repository->paginateTransactions(15, $filters);
        $rooms = Room::all();
        $items = $this->repository->getAllActiveItems();

        return view('refreshments.transactions.index', compact('transactions', 'rooms', 'items'));
    }

    public function create()
    {
        // Only checked-in bookings can consume refreshments
        $bookings = Booking::where('status', Booking::STATUS_CHECKED_IN)->with('room')->get();
        $items = $this->repository->getAllActiveItems();
        return view('refreshments.transactions.create', compact('bookings', 'items'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'booking_id' => 'required|exists:bookings,id',
            'item_id' => 'required|exists:room_refreshment_items,id',
            'quantity' => 'required|integer|min:1'
        ]);

        $this->service->recordConsumption(
            $request->booking_id,
            $request->item_id,
            $request->quantity,
            auth()->id()
        );

        return redirect()->route('refreshments.transactions.index')->with('success', 'Consumption recorded successfully');
    }

    public function report(Request $request)
    {
        $from = $request->filled('from') ?\Carbon\Carbon::parse($request->from)->startOfDay() : now()->startOfMonth();
        $to = $request->filled('to') ?\Carbon\Carbon::parse($request->to)->endOfday() : now()->endOfMonth();

        $itemStats = \App\Models\RoomRefreshmentTransaction::select('item_id',
            \Illuminate\Support\Facades\DB::raw('SUM(quantity) as total_quantity'),
            \Illuminate\Support\Facades\DB::raw('SUM(total_price) as total_revenue'))
            ->whereBetween('created_at', [$from, $to])
            ->groupBy('item_id')
            ->with('item')
            ->orderByDesc('total_quantity')
            ->get();

        return view('refreshments.reports.item-sales', compact('itemStats', 'from', 'to'));
    }
}
