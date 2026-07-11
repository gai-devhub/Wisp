<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    protected $table = 'activity_log';

    protected $fillable = ['user_id', 'activity', 'details'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function log(?int $userId, string $activity, ?string $details = null): self
    {
        return self::create([
            'user_id' => $userId,
            'activity' => $activity,
            'details' => $details,
        ]);
    }
}
