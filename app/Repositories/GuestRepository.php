<?php

namespace App\Repositories;

use App\Models\Guest;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class GuestRepository
{
    public function __construct(
        protected Guest $model
    ) {}

    public function all(): Collection
    {
        return $this->model->newQuery()->orderBy('last_name')->orderBy('first_name')->get();
    }

    public function getByIds(array $ids): Collection
    {
        if (empty($ids)) {
            return collect();
        }
        return $this->model->newQuery()->whereIn('id', $ids)->get();
    }

    public function find(int $id): ?Guest
    {
        return $this->model->find($id);
    }

    public function search(string $query): Collection
    {
        return $this->model->newQuery()
            ->where(function ($q) use ($query) {
                $q->where('first_name', 'like', "%{$query}%")
                    ->orWhere('last_name', 'like', "%{$query}%")
                    ->orWhere('email', 'like', "%{$query}%")
                    ->orWhere('phone', 'like', "%{$query}%");
            })
            ->orderBy('last_name')
            ->limit(50)
            ->get();
    }

    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->newQuery()
            ->orderByDesc('created_at')
            ->paginate($perPage);
    }

    /**
     * Paginate with optional search (name, email, phone).
     */
    public function paginateWithSearch(int $perPage = 15, ?string $search = null): LengthAwarePaginator
    {
        $q = $this->model->newQuery()->orderByDesc('created_at');
        if ($search !== null && trim($search) !== '') {
            $term = '%' . trim($search) . '%';
            $q->where(function ($query) use ($term) {
                $query->where('first_name', 'like', $term)
                    ->orWhere('last_name', 'like', $term)
                    ->orWhere('email', 'like', $term)
                    ->orWhere('phone', 'like', $term);
            });
        }
        return $q->paginate($perPage)->withQueryString();
    }

    public function create(array $data): Guest
    {
        return $this->model->create($data);
    }

    public function update(Guest $guest, array $data): Guest
    {
        $guest->update($data);
        return $guest->fresh();
    }

    public function delete(Guest $guest): bool
    {
        return $guest->delete();
    }
}
