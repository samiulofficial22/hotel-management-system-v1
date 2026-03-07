<?php

namespace App\Repositories;

use App\Models\LaundryItem;
use Illuminate\Database\Eloquent\Collection;

class LaundryItemRepository
{
    public function all(): Collection
    {
        return LaundryItem::all();
    }

    public function find(int $id): ?LaundryItem
    {
        return LaundryItem::find($id);
    }

    public function create(array $data): LaundryItem
    {
        return LaundryItem::create($data);
    }

    public function update(LaundryItem $item, array $data): bool
    {
        return $item->update($data);
    }

    public function delete(LaundryItem $item): bool
    {
        return $item->delete();
    }
}
