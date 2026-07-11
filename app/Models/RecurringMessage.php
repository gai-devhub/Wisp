<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RecurringMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'wish_message_id',
        'frequency',
        'channel',
        'recipient_contact',
        'next_run_at',
        'is_active',
    ];

    protected $casts = [
        'next_run_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function wishMessage(): BelongsTo
    {
        return $this->belongsTo(WishMessages::class, 'wish_message_id');
    }
}
