<?php

namespace App\Repositories;

use App\Models\LaundryOrder;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class LaundryOrderRepository
{
    public function paginate(int $perPage = 15, ?string $status = null): LengthAwarePaginator
    {
        $query = LaundryOrder::with(['room', 'guest', 'createdBy']);
        if ($status) {
            $query->where('status', $status);
        }
        return $query->latest()->paginate($perPage);
    }

    public function find(int $id): ?LaundryOrder
    {
        return LaundryOrder::with(['items.item', 'room', 'guest'])->find($id);
    }

    public function create(array $data): LaundryOrder
    {
        return LaundryOrder::create($data);
    }

    public function update(LaundryOrder $order, array $data): bool
    {
        return $order->update($data);
    }

    public function delete(LaundryOrder $order): bool
    {
        return $order->delete();
    }
}
