<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PosTable extends Model
{
    protected $table = 'pos_tables';

    protected $fillable = ['outlet_id', 'name', 'capacity', 'status', 'sort_order'];

    public const STATUS_AVAILABLE = 'available';
    public const STATUS_BOOKED = 'booked';

    /** @return BelongsTo<Outlet, $this> */
    public function outlet(): BelongsTo
    {
        return $this->belongsTo(Outlet::class);
    }

    /** @return HasMany<PosOrder, $this> */
    public function posOrders(): HasMany
    {
        return $this->hasMany(PosOrder::class);
    }
}
