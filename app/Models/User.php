<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'role',
        'status',
        'google_id',
        'spotify_id',
        'spotify_token',
        'spotify_refresh_token',
        'profile_picture',
        'last_login_at',
        'last_logout_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>    
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login_at' => 'datetime',
            'last_logout_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function getProfilePictureUrlAttribute()
    {
        if (! empty($this->profile_picture)) {
            $url = trim($this->profile_picture);
            
            // Full URL (e.g. Google avatar) – use as-is
            if (str_starts_with($url, 'http://') || str_starts_with($url, 'https://')) {
                return $url;
            }
            
            // If the database stored a local device 'file://' URI from a mobile app previously, it's invalid here
            if (str_starts_with($url, 'file://')) {
                return 'https://ui-avatars.com/api/?name=' . urlencode($this->username ?? 'User') . '&color=7F9CF5&background=EBF4FF';
            }

            // Remove 'profile-pictures/' prefix if it exists to avoid duplication
            $filename = str_replace('profile-pictures/', '', $url);
            
            // Local path (uploaded file)
            return asset('storage/profile-pictures/' . $filename);
        }

        return 'https://ui-avatars.com/api/?name=' . urlencode($this->username ?? 'User') . '&color=7F9CF5&background=EBF4FF';
    }

    public function wishMessages()
    {
        return $this->hasMany(WishMessages::class);
    }

    /**
     * Returns true if the user has an active premium subscription
     * (either paid or admin-granted).
     */
    public function isPremium(): bool
    {
        return \App\Models\Subscription::where('user_id', $this->id)
            ->where('status', 'active')
            ->where(function ($q) {
                $q->where('expires_at', '>', now())
                  ->orWhere('admin_granted', true);
            })
            ->exists();
    }

    /**
     * Returns the number of non-deleted messages owned by this user.
     */
    public function freeMessageCount(): int
    {
        return \App\Models\WishMessages::where('user_id', $this->id)->count();
    }

    public function views()
    {
        return $this->hasMany(MessageViews::class);
    }


    public function generatedLink()
    {
        return $this->hasMany(GeneratedLinks::class);
    }

    public function mediaFiles()
    {
        return $this->hasMany(MediaFiles::class);
    }

    public function settings()
    {
        return $this->hasOne(UserSettings::class);
    }

    public function userNotifications()
    {
        return $this->hasMany(UserNotification::class, 'user_id');
    }

    public function sentNotifications()
    {
        return $this->hasMany(UserNotification::class, 'sender_id');
    }

    public function notificationBroadcasts()
    {
        return $this->hasMany(NotificationBroadcast::class, 'sent_by');
    }

    /**
     * Calculate total storage used by the user in bytes.
     */
    public function totalStorageUsage(): int
    {
        $mediaFiles = $this->mediaFiles;
        $totalSize = 0;
        foreach ($mediaFiles as $file) {
            if ($file->recipient_image && \Illuminate\Support\Facades\Storage::disk('s3')->exists($file->recipient_image)) {
                $totalSize += \Illuminate\Support\Facades\Storage::disk('s3')->size($file->recipient_image);
            }
            if ($file->background_music && \Illuminate\Support\Facades\Storage::disk('s3')->exists($file->background_music)) {
                $totalSize += \Illuminate\Support\Facades\Storage::disk('s3')->size($file->background_music);
            }
        }
        return $totalSize;
    }

    /**
     * Check if the user has reached their 1GB storage limit.
     */
    public function isStorageFull(): bool
    {
        $limit = 1024 * 1024 * 1024; // 1GB in bytes
        return $this->totalStorageUsage() >= $limit;
    }
}