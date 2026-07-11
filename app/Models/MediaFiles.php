<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MediaFiles extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'wish_message_id',
        'recipient_image',
        'background_music',
        'apple_music_url',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function wishMessage()
    {
        return $this->belongsTo(WishMessages::class, 'wish_message_id');
    }

    }