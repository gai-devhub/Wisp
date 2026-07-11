<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Models\WishMessages;
use App\Http\Controllers\ShareMessagesController;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Crypt;

class ProcessAutomations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:process-automations';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process and send scheduled recurring messages';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting automation processing...');


        $this->processScheduledShares();
        $this->processExpiredPages();

        $this->info('Automation processing completed.');
    }

    private function processExpiredPages()
    {
        $settings = \App\Models\UserSettings::where('auto_delete_expired', '!=', 'never')
            ->whereNotNull('auto_delete_expired')
            ->get();

        $usersByDelay = [];
        foreach ($settings as $setting) {
            $usersByDelay[$setting->auto_delete_expired][] = $setting->user_id;
        }

        $totalDeleted = 0;

        foreach ($usersByDelay as $delay => $userIds) {
            $threshold = now();
            switch ($delay) {
                case 'immediately':
                    $threshold = now();
                    break;
                case '1_hour':
                    $threshold = now()->subHour();
                    break;
                case '1_day':
                    $threshold = now()->subDay();
                    break;
                case '1_week':
                    $threshold = now()->subWeek();
                    break;
                case '1_month':
                    $threshold = now()->subMonth();
                    break;
                case '2_months':
                    $threshold = now()->subMonths(2);
                    break;
                case '6_months':
                    $threshold = now()->subMonths(6);
                    break;
                default:
                    continue 2;
            }

            $messagesToDelete = \App\Models\WishMessages::whereNotNull('expires_at')
                ->where('expires_at', '<=', $threshold)
                ->whereIn('user_id', $userIds)
                ->get();

            foreach ($messagesToDelete as $msg) {
                $msg->delete();
                $totalDeleted++;
            }
        }

        if ($totalDeleted > 0) {
            $this->info("Deleted {$totalDeleted} expired messages based on user preferences.");
        }
    }

    private function processScheduledShares()
    {
        $scheduledShares = \App\Models\ShareSend::where('status', 'scheduled')
            ->where('scheduled_at', '<=', now())
            ->with('wishMessage.user')
            ->get();

        $this->info("Found {$scheduledShares->count()} scheduled shares to send.");

        foreach ($scheduledShares as $share) {
            $message = $share->wishMessage;
            
            if (!$message || !$message->generated_link) {
                $share->update(['status' => 'failed', 'error_message' => 'Missing message or link.']);
                $this->error("Skipping scheduled share {$share->id} due to missing message or link.");
                continue;
            }

            try {
                $this->deliverMessage($share->channel, $share->recipient_contact, $message, $share->custom_message);
                $share->update(['status' => 'sent', 'error_message' => null]);
                $this->info("Sent scheduled share {$share->id}");
            } catch (\Exception $e) {
                $share->update(['status' => 'failed', 'error_message' => $e->getMessage()]);
                $this->error("Failed to send scheduled share {$share->id}: " . $e->getMessage());
            }
        }
    }

    private function deliverMessage($channel, $recipient, $message, $customMessage = null)
    {
        $link = $message->generated_link;
        $vaultPin = null;

        if ($message->is_vaulted && $message->specific_vault_pin) {
            try {
                $vaultPin = Crypt::decryptString($message->specific_vault_pin);
            } catch (\Exception $e) {}
        }

        if ($channel === 'email') {
            Mail::send('emails.share-message', [
                'recipientName' => $message->recipient_name ?? 'Friend',
                'senderName' => $message->user?->name ?? $message->user?->username ?? 'A friend',
                'messageTitle' => $message->title,
                'messageBody' => $message->wish_message ?? $message->message,
                'link' => $link,
                'vaultPin' => $vaultPin,
            ], function ($mail) use ($recipient, $message) {
                $mail->to($recipient)
                    ->subject('A message for you: ' . $message->title);
            });
        } elseif ($channel === 'sms') {
            $body = $customMessage ?: ShareMessagesController::buildShareMessage($message->recipient_name ?? 'Friend', $link, $vaultPin);
            
            $phone = $recipient;
            if (!preg_match('/^\+/', $phone)) {
                $phone = '+' . ltrim($phone, '0');
            }

            $twilioClientClass = 'Twilio\Rest\Client';
            if (!class_exists($twilioClientClass) || !config('services.twilio.sid')) {
                throw new \Exception('Twilio not configured or installed.');
            }

            $client = new $twilioClientClass(
                config('services.twilio.sid'),
                config('services.twilio.token')
            );
            $client->messages->create($phone, [
                'from' => config('services.twilio.from'),
                'body' => $body,
            ]);
        } elseif ($channel === 'whatsapp') {
            $body = $customMessage ?: ShareMessagesController::buildShareMessage($message->recipient_name ?? 'Friend', $link, $vaultPin);
            
            $phone = $recipient;
            if (!preg_match('/^\+/', $phone)) {
                $phone = '+' . ltrim($phone, '0');
            }
            $phone = 'whatsapp:' . $phone;

            $twilioClientClass = 'Twilio\Rest\Client';
            if (!class_exists($twilioClientClass) || !config('services.twilio.sid')) {
                throw new \Exception('Twilio not configured or installed.');
            }

            $client = new $twilioClientClass(
                config('services.twilio.sid'),
                config('services.twilio.token')
            );
            $client->messages->create($phone, [
                'from' => 'whatsapp:' . config('services.twilio.whatsapp_from', config('services.twilio.from')),
                'body' => $body,
            ]);
        } else {
            throw new \Exception("Unknown channel: {$channel}");
        }
    }
}
