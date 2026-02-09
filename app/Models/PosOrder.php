<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PosOrder extends Model
{
    protected $fillable = [
        'outlet_id', 'pos_table_id', 'order_number', 'status',
        'guest_id', 'booking_id', 'invoice_id', 'pos_type', 'payment_status',
        'subtotal', 'tax_amount', 'total', 'served_by', 'notes', 'completed_at',
    ];

    protected $attributes = [
        'payment_status' => 'pending',
        'pos_type' => 'restaurant',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'total' => 'decimal:2',
        'completed_at' => 'datetime',
    ];

    public const STATUS_OPEN = 'open';
    public const STATUS_SENT_TO_KITCHEN = 'sent_to_kitchen';
    public const STATUS_PREPARING = 'preparing';
    public const STATUS_READY = 'ready';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_CANCELLED = 'cancelled';

    public const POS_TYPE_FRONT_DESK = 'front_desk';
    public const POS_TYPE_RESTAURANT = 'restaurant';
    public const POS_TYPE_ROOM_SERVICE = 'room_service';
    public const POS_TYPE_MINIBAR = 'minibar';
    public const POS_TYPE_LAUNDRY = 'laundry';

    public const PAYMENT_STATUS_PENDING = 'pending';
    public const PAYMENT_STATUS_PAID = 'paid';
    public const PAYMENT_STATUS_POSTED_TO_ROOM = 'posted_to_room';
    public const PAYMENT_STATUS_VOIDED = 'voided';

    /** @return BelongsTo<Outlet, $this> */
    public function outlet(): BelongsTo
    {
        return $this->belongsTo(Outlet::class);
    }

    /** @return BelongsTo<Guest, $this> */
    public function guest(): BelongsTo
    {
        return $this->belongsTo(Guest::class);
    }

    /** @return BelongsTo<Booking, $this> */
    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    /** @return BelongsTo<Invoice, $this> */
    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    /** @return BelongsTo<PosTable, $this> */
    public function posTable(): BelongsTo
    {
        return $this->belongsTo(PosTable::class);
    }

    /** @return BelongsTo<User, $this> */
    public function servedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'served_by');
    }

    /** @return HasMany<PosOrderItem, $this> */
    public function items(): HasMany
    {
        return $this->hasMany(PosOrderItem::class, 'pos_order_id');
    }

    /** @return HasMany<Payment, $this> */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class, 'pos_order_id');
    }

    public function isPaid(): bool
    {
        return $this->payment_status === self::PAYMENT_STATUS_PAID;
    }

    public function isPostedToRoom(): bool
    {
        return $this->payment_status === self::PAYMENT_STATUS_POSTED_TO_ROOM;
    }

    public function isVoided(): bool
    {
        return $this->payment_status === self::PAYMENT_STATUS_VOIDED;
    }

    public function canVoid(): bool
    {
        return $this->payment_status !== self::PAYMENT_STATUS_VOIDED;
    }
}
