<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendNotificationEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /** @var int Timeout in seconds (e.g. 60 for slow SMTP). */
    public $timeout = 60;

    public function __construct(
        public string $toEmail,
        public string $title,
        public string $message
    ) {}

    public function handle(): void
    {
        Mail::raw($this->message, function ($mail) {
            $mail->to($this->toEmail)->subject('Notification: ' . $this->title);
        });
    }
}
