<?php

namespace App\Services;

use App\Models\Attendance;
use App\Repositories\AttendanceRepository;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

class AttendanceService
{
    public function __construct(protected AttendanceRepository $repository)
    {
    }

    public function forEmployeeAndMonth(int $employeeId, Carbon $month): Collection
    {
        return $this->repository->forEmployeeAndMonth($employeeId, $month);
    }

    public function forDate(Carbon $date): Collection
    {
        return $this->repository->forDate($date);
    }

    public function find(int $id): ?Attendance
    {
        return $this->repository->find($id);
    }

    public function markAttendance(int $employeeId, Carbon $date, ?string $checkIn = null, ?string $checkOut = null, string $status = 'present', ?string $notes = null, ?float $overtimeHours = null): Attendance
    {
        $a = $this->repository->firstOrCreateForEmployeeDate($employeeId, $date);
        $data = [
            'check_in' => $checkIn ?: null,
            'check_out' => $checkOut ?: null,
            'status' => $status,
            'overtime_hours' => $overtimeHours,
        ];
        if ($notes !== null && $notes !== '') {
            $data['notes'] = $notes;
        }
        $this->repository->update($a, $data);
        return $a->fresh();
    }

    public function update(Attendance $attendance, array $data): Attendance
    {
        return $this->repository->update($attendance, $data);
    }
}
