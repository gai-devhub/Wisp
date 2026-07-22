<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class WishMessages extends Model
{
    use HasFactory, SoftDeletes;



    protected $table = 'wish_messages';

    protected $fillable = [
        'user_id',
        'message_type',
        'title',
        'recipient_name',
        'recipient_special_name',
        'recipient_phone',
        'greeting',
        'message',
        'wish_message',
        'last_note',
        'receiving_date',
        'sender_name',
        'slug',
        'expiry_hours',
        'expires_at',
        'is_published',
        'is_archived',
        'is_vaulted',
        'specific_vault_pin'
    ];

    protected $casts = [
        'receiving_date' => 'date',
        'expires_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'is_published' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($message) {
            if (empty($message->slug)) {
                $message->slug = Str::slug($message->recipient_name . '-' . Str::random(6));
            }
            
            if (empty($message->expires_at)) {
                $hours = (int) ($message->expiry_hours ?? 24);
                $message->expires_at = $message->receiving_date
                    ? Carbon::parse($message->receiving_date)->setTime(now()->hour, now()->minute, now()->second)->addHours($hours)
                    : now()->addHours($hours);
            }
        });
    }

    /**
     * Scope to messages belonging to the given user (or current user).
     */
    public function scopeForUser($query, $userId = null)
    {
        $userId = $userId ?? Auth::id();
        return $query->where('user_id', $userId);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function template()
    {
        return $this->hasOne(Template::class, 'wish_message_id');
    }

    public function views()
    {
        return $this->hasMany(MessageViews::class, 'wish_message_id');
    }

    // public function shareMessages()
    // {
    //     return $this->hasMany(ShareMessages::class);
    // }

    public function mediaFiles()
    {
        return $this->hasOne(MediaFiles::class, 'wish_message_id');
    }

    public function shareSends()
    {
        return $this->hasMany(ShareSend::class, 'wish_message_id');
    }

    public function generatedLinks()
    {
        return $this->hasMany(GeneratedLinks::class, 'wish_message_id');
    }

    /**
     * Message body with line breaks preserved for display (use in views with {!! $message->message_display !!}).
     */
    public function getMessageDisplayAttribute(): string
    {
        $raw = $this->getRawOriginal('message') ?? $this->getRawOriginal('wish_message') ?? '';
        return nl2br(e($raw));
    }

    /**
     * URL path segment for this message's type (e.g. birthday, vows).
     */
    public function getMessageTypePathAttribute(): string
    {
        return $this->message_type; // use enum value as path segment
    }

    /**
     * Generated shareable link; only set after user generates a link in the Links section.
     */
    public function getGeneratedLinkAttribute()
    {
        $link = $this->generatedLinks()->latest()->first();
        return $link ? $link->generated_url : null;
    }

    /**
     * Returns true if the latest generated link is active.
     */
    public function getIsLinkActiveAttribute()
    {
        $link = $this->generatedLinks()->latest()->first();
        return $link ? (bool) $link->is_active : false;
    }

    public function getViewCountAttribute()
    {
        return $this->views()->count();
    }

    public function getStatusAttribute()
    {
        if (!$this->is_published) {
            return 'draft';
        }

        if ($this->expires_at && $this->expires_at->isPast()) {
            return 'expired';
        }

        if ($this->expires_at && $this->expires_at->diffInHours(now()) <= 24) {
            return 'expiring_soon';
        }

        return 'active';
    }

    public function getSpecificVaultPinAttribute($value)
    {
        if (empty($value)) return $value;
        try {
            $decrypted = \Illuminate\Support\Facades\Crypt::decryptString($value);
            $len = strlen($decrypted);
            if ($len <= 2) {
                return str_repeat('*', $len);
            } elseif ($len <= 4) {
                return substr($decrypted, 0, 1) . str_repeat('*', $len - 2) . substr($decrypted, -1);
            } else {
                return substr($decrypted, 0, 2) . str_repeat('*', $len - 4) . substr($decrypted, -2);
            }
        } catch (\Exception $e) {
            return $value;
        }
    }
}