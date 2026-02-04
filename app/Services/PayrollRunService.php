<?php

namespace App\Services;

use App\Models\PayrollRun;
use App\Models\PayrollItem;
use App\Repositories\PayrollRunRepository;
use App\Repositories\EmployeeRepository;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class PayrollRunService
{
    public function __construct(
        protected PayrollRunRepository $repository,
        protected EmployeeRepository $employeeRepository
    ) {}

    public function find(int $id): ?PayrollRun
    {
        return $this->repository->find($id);
    }

    public function paginate(int $perPage = 10): LengthAwarePaginator
    {
        return $this->repository->paginate($perPage);
    }

    public function create(Carbon $periodStart, Carbon $periodEnd, ?string $title = null): PayrollRun
    {
        return DB::transaction(function () use ($periodStart, $periodEnd, $title) {
            $run = $this->repository->create([
                'title' => $title ?? 'Payroll ' . $periodStart->format('M Y'),
                'period_start' => $periodStart,
                'period_end' => $periodEnd,
                'status' => PayrollRun::STATUS_DRAFT,
            ]);
            $employees = $this->employeeRepository->all(true);
            foreach ($employees as $emp) {
                PayrollItem::create([
                    'payroll_run_id' => $run->id,
                    'employee_id' => $emp->id,
                    'base_salary' => $emp->base_salary,
                    'allowances' => 0,
                    'deductions' => 0,
                    'net_salary' => $emp->base_salary,
                ]);
            }
            return $run->fresh();
        });
    }

    public function process(PayrollRun $run, int $userId): PayrollRun
    {
        $run->update(['status' => PayrollRun::STATUS_PROCESSED, 'processed_at' => now(), 'processed_by' => $userId]);
        return $run->fresh();
    }
}
