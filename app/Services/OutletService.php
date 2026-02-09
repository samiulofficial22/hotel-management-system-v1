<?php

namespace App\Services;

use App\Models\Outlet;
use App\Repositories\OutletRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Validation\ValidationException;

class OutletService
{
    public function __construct(protected OutletRepository $repository) {}

    public function rules(bool $forUpdate = false): array
    {
        $codeRule = $forUpdate ? 'nullable|string|max:20|unique:outlets,code,' : 'nullable|string|max:20|unique:outlets,code';
        return [
            'name' => ['required', 'string', 'max:100'],
            'type' => ['required', 'in:restaurant,cafe,bar'],
            'code' => [$codeRule],
            'is_active' => ['boolean'],
        ];
    }

    public function all(bool $activeOnly = true): Collection
    {
        return $this->repository->all($activeOnly);
    }

    public function find(int $id): ?Outlet
    {
        return $this->repository->find($id);
    }

    /** @throws ValidationException */
    public function create(array $data): Outlet
    {
        $data['is_active'] = $data['is_active'] ?? true;
        return $this->repository->create($data);
    }

    /** @throws ValidationException */
    public function update(Outlet $outlet, array $data): Outlet
    {
        return $this->repository->update($outlet, $data);
    }

    /** @throws ValidationException */
    public function delete(Outlet $outlet): void
    {
        if ($outlet->posOrders()->exists()) {
            throw ValidationException::withMessages(['outlet' => __('Cannot delete outlet: it has POS orders.')]);
        }
        if ($outlet->posTables()->exists()) {
            throw ValidationException::withMessages(['outlet' => __('Cannot delete outlet: it has tables.')]);
        }
        if ($outlet->menuCategories()->exists()) {
            throw ValidationException::withMessages(['outlet' => __('Cannot delete outlet: it has menu categories. Remove or reassign them first.')]);
        }
        $outlet->delete();
    }
}
