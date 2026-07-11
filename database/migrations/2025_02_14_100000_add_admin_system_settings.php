<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (!\Schema::hasTable('system_settings')) {
            return;
        }
        $now = now()->toDateTimeString();
        $defaults = [
            'session_timeout' => '30',
            'max_login_attempts' => '5',
            'timezone' => 'UTC',
            'date_format' => 'd/m/Y',
            'items_per_page' => '25',
            'notify_email_alerts' => '1',
            'notify_push_critical' => '1',
            'notify_daily_summary' => '0',
        ];
        foreach ($defaults as $key => $value) {
            $exists = DB::table('system_settings')->where('key', $key)->exists();
            if ($exists) {
                DB::table('system_settings')->where('key', $key)->update(['value' => $value, 'updated_at' => $now]);
            } else {
                DB::table('system_settings')->insert(['key' => $key, 'value' => $value, 'created_at' => $now, 'updated_at' => $now]);
            }
        }
    }

    public function down(): void
    {
        if (!\Schema::hasTable('system_settings')) {
            return;
        }
        DB::table('system_settings')->whereIn('key', [
            'session_timeout', 'max_login_attempts', 'timezone', 'date_format', 'items_per_page',
            'notify_email_alerts', 'notify_push_critical', 'notify_daily_summary',
        ])->delete();
    }
};
