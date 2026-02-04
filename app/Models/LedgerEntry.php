<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LedgerEntry extends Model
{
    protected $fillable = ['entry_date', 'account_id', 'debit', 'credit', 'reference_type', 'reference_id', 'description', 'created_by'];

    protected $casts = ['entry_date' => 'date', 'debit' => 'decimal:2', 'credit' => 'decimal:2'];

    /** @return BelongsTo<ChartOfAccount, $this> */
    public function account(): BelongsTo
    {
        return $this->belongsTo(ChartOfAccount::class, 'account_id');
    }

    /** @return BelongsTo<User, $this> */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
