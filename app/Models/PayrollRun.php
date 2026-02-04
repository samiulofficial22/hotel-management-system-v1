<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PayrollRun extends Model
{
    protected $fillable = ['title', 'period_start', 'period_end', 'status', 'processed_at', 'processed_by'];

    protected $casts = ['period_start' => 'date', 'period_end' => 'date', 'processed_at' => 'datetime'];

    public const STATUS_DRAFT = 'draft';
    public const STATUS_PROCESSED = 'processed';
    public const STATUS_PAID = 'paid';

    /** @return HasMany<PayrollItem, $this> */
    public function items(): HasMany
    {
        return $this->hasMany(PayrollItem::class, 'payroll_run_id');
    }

    /** @return BelongsTo<User, $this> */
    public function processedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by');
    }
}
