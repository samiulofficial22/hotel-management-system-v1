<?php

namespace App\Services;

use App\Models\PayrollRun;
use App\Models\PayrollItem;
use App\Repositories\PayrollRunRepository;
use App\Repositories\EmployeeRepository;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PayrollRunService
{
    public function __construct(
        protected PayrollRunRepository $repository,
        protected EmployeeRepository $employeeRepository,
        protected LedgerEntryService $ledgerService,
        protected ChartOfAccountService $chartService
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
            
            // Fetch attendances for the period to calculate working days and overtime
            $attendances = \App\Models\Attendance::whereBetween('date', [$periodStart->toDateString(), $periodEnd->toDateString()])
                ->get()
                ->groupBy('employee_id');

            foreach ($employees as $emp) {
                $base = (float) ($emp->salary ?? $emp->base_salary ?? 0);
                
                $empAttendances = $attendances->get($emp->id, collect());
                
                $workingDays = $empAttendances->sum(function($att) {
                    return $att->status === \App\Models\Attendance::STATUS_HALF_DAY ? 0.5 : 
                           ($att->status === \App\Models\Attendance::STATUS_PRESENT ? 1 : 0);
                });
                
                // standard hotel monthly overtime rate = base / 30 days / 8 hours
                $totalOvertimeHours = $empAttendances->sum('overtime_hours');
                $hourlyRate = $base > 0 ? ($base / 30 / 8) : 0;
                $overtimeAmount = (float) round($totalOvertimeHours * $hourlyRate, 2);

                $netSalary = $base + $overtimeAmount;

                PayrollItem::create([
                    'payroll_run_id'  => $run->id,
                    'employee_id'     => $emp->id,
                    'base_salary'     => $base,
                    'working_days'    => $workingDays,
                    'overtime_amount' => $overtimeAmount,
                    'allowances'      => 0,
                    'deductions'      => 0,
                    'net_salary'      => $netSalary,
                ]);
            }
            return $run->fresh();
        });
    }

    public function process(PayrollRun $run, int $userId): PayrollRun
    {
        if ($run->status !== PayrollRun::STATUS_DRAFT) {
            throw new ValidationException(null, __('Payroll can only be approved from draft.'));
        }
        $run->update(['status' => PayrollRun::STATUS_PROCESSED, 'processed_at' => now(), 'processed_by' => $userId]);
        return $run->fresh();
    }

    /**
     * Mark payroll as paid: create ledger entry (Salary Expense Dr, Cash/Bank Cr) and set paid_at.
     * Only Admin or Accountant should call this (enforce via permission payroll.pay).
     */
    public function markAsPaid(PayrollRun $run, int $userId, int $paymentAccountId): PayrollRun
    {
        if ($run->status !== PayrollRun::STATUS_PROCESSED) {
            throw new ValidationException(null, __('Payroll must be approved before it can be paid.'));
        }
        $salaryExpenseAccount = $this->chartService->findByCode('SALARY_EXP');
        if (! $salaryExpenseAccount) {
            throw new ValidationException(null, __('Salary Expense account not found. Run Chart of Accounts seeder.'));
        }
        $totalNet = (float) $run->items()->sum('net_salary');
        if ($totalNet <= 0) {
            throw new ValidationException(null, __('Total net salary is zero.'));
        }
        return DB::transaction(function () use ($run, $userId, $paymentAccountId, $salaryExpenseAccount, $totalNet) {
            $this->ledgerService->createDoubleEntry(
                $salaryExpenseAccount->id,
                $paymentAccountId,
                $totalNet,
                now()->toDateString(),
                'Payroll paid: ' . $run->title . ' (' . $run->period_start->format('M Y') . ')',
                'payroll_run',
                $run->id,
                $userId
            );
            $run->update([
                'status' => PayrollRun::STATUS_PAID,
                'paid_at' => now(),
                'paid_by' => $userId,
            ]);
            return $run->fresh();
        });
    }


    public function revert(PayrollRun $run): PayrollRun
    {
        if ($run->status === PayrollRun::STATUS_PAID) {
            throw new \Illuminate\Validation\ValidationException(null, __('Paid payroll cannot be reverted.'));
        }

        $run->update([
            'status' => PayrollRun::STATUS_DRAFT,
            'processed_at' => null,
            'processed_by' => null,
        ]);

        return $run->fresh();
    }

    public function delete(PayrollRun $run): void
    {
        if ($run->status === PayrollRun::STATUS_PAID) {
            throw new \Illuminate\Validation\ValidationException(null, __('Paid payroll cannot be deleted.'));
        }

        \Illuminate\Support\Facades\DB::transaction(function () use ($run) {
            $run->items()->delete();
            $run->delete();
        });
    }
}
