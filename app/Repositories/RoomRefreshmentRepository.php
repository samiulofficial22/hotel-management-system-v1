<?php

namespace App\Repositories;

use App\Models\RoomRefreshmentItem;
use App\Models\RoomRefreshmentTransaction;

class RoomRefreshmentRepository
{
    public function paginateItems(int $perPage = 15)
    {
        return RoomRefreshmentItem::latest()->paginate($perPage);
    }

    public function getAllActiveItems()
    {
        return RoomRefreshmentItem::where('is_active', true)->get();
    }

    public function createItem(array $data)
    {
        return RoomRefreshmentItem::create($data);
    }

    public function findItem(int $id)
    {
        return RoomRefreshmentItem::findOrFail($id);
    }

    public function updateItem(int $id, array $data)
    {
        $item = $this->findItem($id);
        $item->update($data);
        return $item;
    }

    public function deleteItem(int $id)
    {
        return RoomRefreshmentItem::destroy($id);
    }

    public function createTransaction(array $data)
    {
        return RoomRefreshmentTransaction::create($data);
    }

    public function paginateTransactions(int $perPage = 15, $filters = [])
    {
        $query = RoomRefreshmentTransaction::with(['booking', 'room', 'item', 'recordedBy']);

        if (!empty($filters['room_id'])) {
            $query->where('room_id', $filters['room_id']);
        }
        if (!empty($filters['item_id'])) {
            $query->where('item_id', $filters['item_id']);
        }
        if (!empty($filters['date'])) {
            $query->whereDate('created_at', $filters['date']);
        }

        return $query->latest()->paginate($perPage);
    }
}
