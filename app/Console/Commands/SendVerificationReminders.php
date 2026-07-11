<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SendVerificationReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mail:send-verification-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send an email verification reminder to users who registered 24 hours ago.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Finding unverified users created ~24 hours ago...');

        // Find users created between 23 and 25 hours ago, who are not verified
        $users = \App\Models\User::whereNull('email_verified_at')
            ->whereBetween('created_at', [
                now()->subHours(25),
                now()->subHours(23)
            ])
            ->get();

        $count = 0;

        foreach ($users as $user) {
            // Generate a signed route that expires in 24 hours
            $url = \Illuminate\Support\Facades\URL::temporarySignedRoute(
                'verification.verify',
                now()->addHours(24),
                ['id' => $user->id, 'hash' => sha1($user->email)]
            );

            \Illuminate\Support\Facades\Mail::to($user->email)
                ->send(new \App\Mail\VerificationReminderEmail($user, $url));

            $this->info("Sent verification reminder to {$user->email}");
            $count++;
        }

        $this->info("Successfully sent {$count} verification reminders.");
    }
}
