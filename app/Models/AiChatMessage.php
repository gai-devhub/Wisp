<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AiChatMessage extends Model
{
    protected $fillable = [
        'user_id',
        'message_type',
        'prompt',
        'response',
        'tokens_used',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
