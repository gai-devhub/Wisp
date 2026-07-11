<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MessageViews extends Model
{
    use HasFactory;

    protected $table = 'message_views';

    protected $fillable = [
        'user_id',
        'wish_message_id',
        'date',
        'ip_address',
        'viewed_at',
    ];

    protected $casts = [
        'viewed_at' => 'datetime',
    ];

    public function wishMessage()
    {
        return $this->belongsTo(WishMessages::class, 'wish_message_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function generatedLink()
    {
        return $this->belongsTo(GeneratedLinks::class, 'wish_message_id', 'wish_message_id');
    }
}