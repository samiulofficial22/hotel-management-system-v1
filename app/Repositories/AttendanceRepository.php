<?php

namespace App\Repositories;

use App\Models\Attendance;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

class AttendanceRepository
{
    public function __construct(protected Attendance $model) {}

    public function forEmployeeAndMonth(int $employeeId, Carbon $month): Collection
    {
        return $this->model->newQuery()
            ->where('employee_id', $employeeId)
            ->whereBetween('date', [$month->copy()->startOfMonth()->toDateString(), $month->copy()->endOfMonth()->toDateString()])
            ->orderBy('date')
            ->get();
    }

    public function forDate(Carbon $date): Collection
    {
        return $this->model->newQuery()->with('employee')->where('date', $date->toDateString())->orderBy('employee_id')->get();
    }

    public function find(int $id): ?Attendance
    {
        return $this->model->with('employee')->find($id);
    }

    public function firstOrCreateForEmployeeDate(int $employeeId, Carbon $date, array $attrs = []): Attendance
    {
        return $this->model->firstOrCreate(
            ['employee_id' => $employeeId, 'date' => $date->toDateString()],
            array_merge(['status' => Attendance::STATUS_PRESENT], $attrs)
        );
    }

    public function update(Attendance $attendance, array $data): Attendance
    {
        $attendance->update($data);
        return $attendance->fresh();
    }
}
