<?php

namespace App\Services;

use App\Models\Guest;
use App\Repositories\GuestRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class GuestService
{
    public function __construct(
        protected GuestRepository $repository
    ) {}

    public function rules(bool $forUpdate = false): array
    {
        return [
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'nationality' => ['nullable', 'string', 'max:100'],
            'id_type' => ['nullable', 'string', 'max:50'],
            'id_number' => ['nullable', 'string', 'max:100'],
            'nid_number' => ['nullable', 'string', 'max:50'],
            'date_of_birth' => ['nullable', 'date'],
            'address' => ['nullable', 'string'],
            'city' => ['nullable', 'string', 'max:100'],
            'country' => ['nullable', 'string', 'max:100'],
            'notes' => ['nullable', 'string'],
        ];
    }

    public function all(): Collection
    {
        return $this->repository->all();
    }

    public function search(string $query): Collection
    {
        return $this->repository->search($query);
    }

    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->paginate($perPage);
    }

    public function paginateWithSearch(int $perPage = 15, ?string $search = null): LengthAwarePaginator
    {
        return $this->repository->paginateWithSearch($perPage, $search);
    }

    public function find(int $id): ?Guest
    {
        return $this->repository->find($id);
    }

    public function create(array $data): Guest
    {
        return $this->repository->create($data);
    }

    public function update(Guest $guest, array $data): Guest
    {
        return $this->repository->update($guest, $data);
    }

    public function delete(Guest $guest): bool
    {
        return $this->repository->delete($guest);
    }
}
