<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PosActionLog extends Model
{
    protected $fillable = ['pos_order_id', 'user_id', 'action', 'details'];

    protected $casts = ['details' => 'array'];

    public const ACTION_CREATE = 'create';
    public const ACTION_ADD_ITEM = 'add_item';
    public const ACTION_REMOVE_ITEM = 'remove_item';
    public const ACTION_COMPLETE = 'complete';
    public const ACTION_PAY = 'pay';
    public const ACTION_POST_TO_ROOM = 'post_to_room';
    public const ACTION_VOID = 'void';

    /** @return BelongsTo<PosOrder, $this> */
    public function posOrder(): BelongsTo
    {
        return $this->belongsTo(PosOrder::class);
    }

    /** @return BelongsTo<User, $this> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
