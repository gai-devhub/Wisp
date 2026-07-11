<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Schedule;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Cache;
use App\Models\User;
use App\Mail\StorageWarningAlert;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('app:process-automations')->everyMinute()->withoutOverlapping();
Schedule::command('mail:send-verification-reminders')->hourly()->withoutOverlapping();

Schedule::call(function () {
    $dirSize = function ($dir) use (&$dirSize) {
        $size = 0;
        if (!is_dir($dir)) return 0;
        foreach (new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($dir, \FilesystemIterator::SKIP_DOTS)) as $file) {
            $size += $file->getSize();
        }
        return $size;
    };

    $uploads = $dirSize(storage_path('app'));
    $logs = $dirSize(storage_path('logs'));
    $totalBytes = $uploads + $logs;

    // 2TB in bytes: 2199023255552
    $twoTB = 2199023255552;
    $percentage = ($totalBytes / $twoTB) * 100;

    if ($percentage >= 90) {
        // Send alert once every 24 hours to prevent inbox spamming
        Cache::remember('storage_warning_sent', now()->addHours(24), function () use ($percentage, $totalBytes) {
            $admins = User::where('role', 'admin')->get();
            
            $formatSize = function ($bytes) {
                if ($bytes < 1024) return $bytes . ' B';
                if ($bytes < 1048576) return round($bytes / 1024, 1) . ' KB';
                if ($bytes < 1073741824) return round($bytes / 1048576, 1) . ' MB';
                return round($bytes / 1073741824, 2) . ' GB';
            };
            $formattedSize = $formatSize($totalBytes);

            foreach ($admins as $admin) {
                Mail::to($admin->email)->send(new StorageWarningAlert($percentage, $formattedSize));
            }
            return true;
        });
    } else {
        Cache::forget('storage_warning_sent');
    }
})->everyTenMinutes();
