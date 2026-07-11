<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class NotificationBroadcast extends Model
{
    protected $table = 'notification_broadcasts';

    protected $fillable = [
        'type',
        'title',
        'message',
        'audience',
        'sent_by',
    ];

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sent_by');
    }

    public function userNotifications(): HasMany
    {
        return $this->hasMany(UserNotification::class, 'notification_broadcast_id');
    }

    public function rootNotifications(): HasMany
    {
        return $this->hasMany(UserNotification::class, 'notification_broadcast_id')
            ->where(function ($query) {
                $query->whereNull('parent_id')
                    ->where(function ($rootQuery) {
                        $rootQuery->whereNull('root_id')
                            ->orWhereColumn('root_id', 'user_notifications.id');
                    });
            });
    }
}