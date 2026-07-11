<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShareSend extends Model
{
    protected $table = 'share_sends';

    protected $fillable = [
        'user_id',
        'wish_message_id',
        'channel',
        'recipient_hash',
        'recipient_masked',
        'recipient_contact',
        'custom_message',
        'status',
        'error_message',
        'scheduled_at',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'recipient_contact' => 'encrypted',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function wishMessage(): BelongsTo
    {
        return $this->belongsTo(WishMessages::class, 'wish_message_id');
    }

    public static function hashRecipient(string $value): string
    {
        return hash('sha256', $value . config('app.key'));
    }

    public static function maskEmail(string $email): string
    {
        $at = strpos($email, '@');
        if ($at === false) {
            return '***@***';
        }
        $local = substr($email, 0, $at);
        $domain = substr($email, $at + 1);
        $maskedLocal = strlen($local) > 1 ? '***' . substr($local, -1) : '***';
        $dot = strrpos($domain, '.');
        $maskedDomain = ($dot !== false) ? '***' . substr($domain, $dot) : '***';
        return $maskedLocal . '@' . $maskedDomain;
    }

    public static function maskPhone(string $phone): string
    {
        $digits = preg_replace('/\D/', '', $phone);
        $len = strlen($digits);
        if ($len <= 4) {
            return '***' . substr($digits, -min(1, $len));
        }
        return '***' . substr($digits, -4);
    }
}
