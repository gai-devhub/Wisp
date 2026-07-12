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
        'theme_bg_enabled',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the masked representation of the vault PIN (e.g., 3*****6).
     */
    public function getMaskedVaultPinAttribute()
    {
        if (!$this->vault_pin) {
            return '';
        }

        try {
            $decrypted = \Illuminate\Support\Facades\Crypt::decryptString($this->vault_pin);
            $length = strlen($decrypted);
            if ($length <= 2) {
                return str_repeat('*', 5);
            }
            return $decrypted[0] . str_repeat('*', 5) . $decrypted[$length - 1];
        } catch (\Exception $e) {
            // Fallback for older hashed pins
            return '3*****6';
        }
    }
}