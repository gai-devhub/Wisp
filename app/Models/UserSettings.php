<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserSettings extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'page_expiry',
        'page_view_alerts',
        'whatsapp_notifications',
        'auto_delete_expired',
        'vault_pin',
        'auto_archive_days',
        'privacy_blur_enabled',
        'theme_preference',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}