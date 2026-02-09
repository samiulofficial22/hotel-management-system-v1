<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attendance extends Model
{
    protected $fillable = ['employee_id', 'date', 'check_in', 'check_out', 'working_hours', 'overtime_hours', 'status', 'notes'];

    protected $casts = [
        'date' => 'date',
        'working_hours' => 'decimal:2',
        'overtime_hours' => 'decimal:2',
    ];

    public const STATUS_PRESENT = 'present';
    public const STATUS_ABSENT = 'absent';
    public const STATUS_HALF_DAY = 'half_day';
    public const STATUS_LEAVE = 'leave';

    /** @return BelongsTo<Employee, $this> */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }
}
