<?php

namespace App\Repositories;

use App\Models\MaintenanceRequest;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class MaintenanceRequestRepository
{
    public function __construct(protected MaintenanceRequest $model) {}

    public function find(int $id): ?MaintenanceRequest
    {
        return $this->model->with(['room', 'reportedBy', 'assignedTo'])->find($id);
    }

    public function paginate(int $perPage = 15, ?string $status = null): LengthAwarePaginator
    {
        $q = $this->model->newQuery()->with(['room', 'reportedBy', 'assignedTo'])->orderByDesc('created_at');
        if ($status !== null) {
            $q->where('status', $status);
        }
        return $q->paginate($perPage);
    }

    public function create(array $data): MaintenanceRequest
    {
        return $this->model->create($data);
    }

    public function update(MaintenanceRequest $req, array $data): MaintenanceRequest
    {
        $req->update($data);
        return $req->fresh();
    }
}
