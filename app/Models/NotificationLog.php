<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class NotificationLog extends Model
{
    protected $fillable = [
        'type',
        'channel',
        'recipient',
        'subject',
        'body',
        'status',
        'external_id',
        'error_message',
        'notifiable_id',
        'notifiable_type',
        'sent_at',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
    ];

    /** @return MorphTo */
    public function notifiable(): MorphTo
    {
        return $this->morphTo();
    }
}
