<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserNotification extends Model
{
    use HasFactory;

    protected $table = 'user_notifications';

    protected $fillable = [
        'user_id',
        'notification_broadcast_id',
        'sender_id',
        'parent_id',
        'root_id',
        'recipient_group',
        'type',
        'title',
        'message',
        'context',
        'meta',
        'is_reply',
        'replied_at',
        'read_at',
    ];

    protected $casts = [
        'meta' => 'array',
        'is_reply' => 'boolean',
        'replied_at' => 'datetime',
        'read_at' => 'datetime',
    ];

    public const TYPE_INFO = 'info';
    public const TYPE_SUCCESS = 'success';
    public const TYPE_WARNING = 'warning';
    public const TYPE_ERROR = 'error';

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function notificationBroadcast()
    {
        return $this->belongsTo(NotificationBroadcast::class, 'notification_broadcast_id');
    }

    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function root()
    {
        return $this->belongsTo(self::class, 'root_id');
    }

    public function replies()
    {
        return $this->hasMany(self::class, 'parent_id')->orderBy('created_at');
    }

    public function scopeUnread($query)
    {
        return $query->whereNull('read_at');
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeRootThreads($query)
    {
        return $query->where(function ($threadQuery) {
            $threadQuery->whereNull('parent_id')
                ->where(function ($rootQuery) {
                    $rootQuery->whereNull('root_id')
                        ->orWhereColumn('root_id', 'id');
                });
        });
    }

    public function scopeInboxForUser($query, $userId)
    {
        return $query->forUser($userId)->rootThreads();
    }

    public function scopeSentBy($query, $userId)
    {
        return $query->where('sender_id', $userId);
    }

    public function isFromAdmin(): bool
    {
        if (! $this->relationLoaded('sender') && $this->sender_id === null) {
            return false;
        }

        return (string) optional($this->sender)->role === 'admin';
    }

    public function getDisplaySenderNameAttribute(): string
    {
        $sender = $this->sender;

        if ($sender) {
            return $sender->name ?: ($sender->username ?: 'Unknown Sender');
        }

        return 'System';
    }
}