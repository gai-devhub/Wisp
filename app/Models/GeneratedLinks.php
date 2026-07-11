<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\WishMessages;

class GeneratedLinks extends Model
{
    use HasFactory;

    protected $fillable = [
        'wish_message_id',
        'generated_url',
        'unique_code',
        'view_count',
        'is_active',
        'expires_at'
    ];

    /**
     * Get the wish message that owns the generated link.
     */
    public function wishMessage()
    {
        return $this->belongsTo(WishMessages::class, 'wish_message_id');
    }

    /**
     * Get the user who owns this link (via the wish message).
     */
    public function user()
    {
        return $this->hasOneThrough(
            User::class,
            WishMessages::class,
            'id',        // Foreign key on wish_messages table
            'id',        // Foreign key on users table
            'wish_message_id', // Local key on generated_links table
            'user_id'    // Local key on wish_messages table
        );
    }
}