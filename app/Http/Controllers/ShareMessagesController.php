<?php

namespace App\Http\Controllers;

use App\Models\ShareSend;
use App\Models\UserNotification;
use App\Models\WishMessages;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class ShareMessagesController extends Controller
{
    /**
     * Load the selected message's link and details for the ShareMessages section (no JS).
     */
    public function loadMessage(Request $request)
    {
        $request->validate(['wish_message_id' => 'required|integer']);
        $message = WishMessages::where('user_id', Auth::id())
            ->find($request->input('wish_message_id'));

        if (!$message) {
            return redirect()->route('user.share-messages.page')
                ->with('error', 'Message not found.');
        }

        $link = $message->generated_link;
        if (!$link) {
            return redirect()->route('user.share-messages.page')
                ->with('error', 'Generate a link for this message first in the Links section.');
        }

        session(['share_messages_id' => $message->id]);
        return redirect()->route('user.share-messages.page');
    }

    /**
     * Return last 2 share sends for the current user (JSON).
     */
    public function recentSends(Request $request)
    {
        $sends = ShareSend::where('user_id', Auth::id())
            ->with('wishMessage')
            ->latest()
            ->take(5)
            ->get();
        return response()->json([
            'recent_sends' => $sends->map(fn ($s) => $this->shareSendForUi($s))->values()->toArray(),
        ]);
    }

    private function shareSendForUi($s): array
    {
        return [
            'id' => $s->id,
            'channel' => $s->channel,
            'recipient_masked' => $s->recipient_masked,
            'status' => $s->status,
            'message_title' => $s->wishMessage?->title ?? 'Message',
            'created_at' => $s->created_at?->toIso8601String(),
            'scheduled_at' => $s->scheduled_at?->toIso8601String(),
        ];
    }

    /**
     * Build the share message text with recipient name and link (backend).
     */
    public static function buildShareMessage(string $recipientName, string $link, ?string $vaultPin = null): string
    {
        $text = "Hey {$recipientName}! I've created a special message for you. Check it out here: {$link}";
        if ($vaultPin) {
            $text .= " (Vault PIN: {$vaultPin})";
        }
        return $text;
    }

    /**
     * Send the message via email (free – Laravel Mail).
     */
    public function sendEmail(Request $request)
    {
        $request->validate([
            'wish_message_id' => 'required|integer',
            'recipient_email' => 'required|email',
        ]);

        $message = WishMessages::where('user_id', Auth::id())
            ->find($request->input('wish_message_id'));
        if (!$message) {
            return $request->wantsJson() ? response()->json(['success' => false, 'error' => 'Message not found.'], 404) : redirect()->route('user.share-messages.page')
                ->with('error', 'Message not found.');
        }

        $link = $message->generated_link;
        if (!$link) {
            return $request->wantsJson() ? response()->json(['success' => false, 'error' => 'Generate a link for this message first.'], 400) : redirect()->route('user.share-messages.page')
                ->with('error', 'Generate a link for this message first.');
        }

        $vaultPin = null;
        if ($message->is_vaulted && $message->specific_vault_pin) {
            try {
                $vaultPin = \Illuminate\Support\Facades\Crypt::decryptString($message->getRawOriginal('specific_vault_pin'));
            } catch (\Exception $e) {}
        }
        $recipientEmail = $request->input('recipient_email');
        $recipientHash = ShareSend::hashRecipient($recipientEmail);
        $recipientMasked = ShareSend::maskEmail($recipientEmail);

        $shareSend = ShareSend::create([
            'user_id' => Auth::id(),
            'wish_message_id' => $message->id,
            'channel' => 'email',
            'recipient_hash' => $recipientHash,
            'recipient_masked' => $recipientMasked,
            'status' => 'sending',
        ]);

        try {
            Mail::send('emails.share-message', [
                'recipientName' => $message->recipient_name,
                'senderName' => Auth::user()?->name ?? Auth::user()?->username ?? 'A friend',
                'messageTitle' => $message->title,
                'messageBody' => $message->wish_message ?? $message->message,
                'link' => $link,
                'vaultPin' => $vaultPin,
            ], function ($mail) use ($recipientEmail, $message) {
                $mail->to($recipientEmail)
                    ->subject('A message for you: ' . $message->title);
            });
            $shareSend->update(['status' => 'sent', 'error_message' => null]);
        } catch (\Throwable $e) {
            $shareSend->update(['status' => 'failed', 'error_message' => $e->getMessage()]);
            if ($request->wantsJson()) {
                $shareSend->load('wishMessage');
                return response()->json(['success' => false, 'error' => $e->getMessage(), 'share_send' => $this->shareSendForUi($shareSend)], 400);
            }
            return redirect()->route('user.share-messages.page')
                ->with('error', 'Could not send email. Check your mail configuration.');
        }

        if ($request->wantsJson()) {
            $shareSend->load('wishMessage');
            return response()->json(['success' => true, 'share_send' => $this->shareSendForUi($shareSend)]);
        }
        return redirect()->route('user.share-messages.page')
            ->with('success', 'Email sent successfully.')
            ->with('success_context', 'share_messages_send');
    }

    /**
     * Send the message via SMS (Twilio – free trial).
     * Phone number can come from the request (recipient_phone) or from the message.
     */
    public function sendSms(Request $request)
    {
        $request->validate([
            'wish_message_id' => 'required|integer',
            'recipient_phone' => 'nullable|string|max:20',
            'custom_message' => 'nullable|string',
        ]);
        $message = WishMessages::where('user_id', Auth::id())
            ->find($request->input('wish_message_id'));
        if (!$message) {
            return $request->wantsJson() ? response()->json(['success' => false, 'error' => 'Message not found.'], 404) : redirect()->route('user.share-messages.page')
                ->with('error', 'Message not found.');
        }

        $phone = $request->input('recipient_phone') ?: $message->recipient_phone;
        $phone = preg_replace('/\s+/', '', trim((string) $phone));
        if (!$phone) {
            return $request->wantsJson() ? response()->json(['success' => false, 'error' => 'Enter recipient phone number or add it to the message.'], 400) : redirect()->route('user.share-messages.page')
                ->with('error', 'Enter recipient phone number or add it to the message.');
        }

        $link = $message->generated_link;
        if (!$link) {
            return $request->wantsJson() ? response()->json(['success' => false, 'error' => 'Generate a link for this message first.'], 400) : redirect()->route('user.share-messages.page')
                ->with('error', 'Generate a link for this message first.');
        }

        $vaultPin = null;
        if ($message->is_vaulted && $message->specific_vault_pin) {
            try {
                $vaultPin = \Illuminate\Support\Facades\Crypt::decryptString($message->getRawOriginal('specific_vault_pin'));
            } catch (\Exception $e) {}
        }

        $body = self::buildShareMessage($message->recipient_name, $link, $vaultPin);
        if (!preg_match('/^\+/', $phone)) {
            $phone = '+' . ltrim($phone, '0');
        }

        if (!config('services.twilio.sid') || !config('services.twilio.token')) {
            return $request->wantsJson() ? response()->json(['success' => false, 'error' => 'Twilio SMS is not configured on the server.'], 400) : redirect()->route('user.share-messages.page')
                ->with('error', 'Twilio SMS is not configured. Set TWILIO_SID, TWILIO_AUTH_TOKEN, TWILIO_FROM in .env');
        }

        $twilioClientClass = 'Twilio\Rest\Client';
        if (!class_exists($twilioClientClass)) {
            return $request->wantsJson() ? response()->json(['success' => false, 'error' => 'Twilio SDK not installed.'], 400) : redirect()->route('user.share-messages.page')
                ->with('error', 'Twilio SDK not installed. Run: composer require twilio/sdk');
        }

        $customMessage = $request->input('custom_message');
        if ($customMessage) {
            $body = $customMessage;
        } else {
            $body = self::buildShareMessage($message->recipient_name ?? 'Friend', $link, $vaultPin);
        }

        $recipientHash = ShareSend::hashRecipient($phone);
        $recipientMasked = ShareSend::maskPhone($phone);

        $shareSend = ShareSend::create([
            'user_id' => Auth::id(),
            'wish_message_id' => $message->id,
            'channel' => 'sms',
            'recipient_hash' => $recipientHash,
            'recipient_masked' => $recipientMasked,
            'recipient_contact' => $phone,
            'custom_message' => $customMessage,
            'status' => 'sending',
        ]);

        try {
            $client = new $twilioClientClass(
                config('services.twilio.sid'),
                config('services.twilio.token')
            );

            $client->messages->create(
                $phone,
                [
                    'from' => config('services.twilio.from'),
                    'body' => $body
                ]
            );
            $shareSend->update(['status' => 'sent', 'error_message' => null]);
        } catch (\Throwable $e) {
            $shareSend->update(['status' => 'failed', 'error_message' => $e->getMessage()]);
            if ($request->wantsJson()) {
                $shareSend->load('wishMessage');
                return response()->json(['success' => false, 'error' => $e->getMessage(), 'share_send' => $this->shareSendForUi($shareSend)], 400);
            }
            return redirect()->route('user.share-messages.page')
                ->with('error', 'Could not send SMS: ' . $e->getMessage());
        }

        if ($request->wantsJson()) {
            $shareSend->load('wishMessage');
            return response()->json(['success' => true, 'share_send' => $this->shareSendForUi($shareSend)]);
        }
        return redirect()->route('user.share-messages.page')
            ->with('success', 'SMS sent successfully.')
            ->with('success_context', 'share_messages_send');
    }

    /**
     * Send the message via WhatsApp (Twilio – free trial).
     */
    public function sendWhatsapp(Request $request)
    {
        $request->validate([
            'wish_message_id' => 'required|integer',
            'recipient_phone' => 'nullable|string|max:20',
            'custom_message' => 'nullable|string',
        ]);
        $message = WishMessages::where('user_id', Auth::id())
            ->find($request->input('wish_message_id'));
        if (!$message) {
            return $request->wantsJson() ? response()->json(['success' => false, 'error' => 'Message not found.'], 404) : redirect()->route('user.share-messages.page')->with('error', 'Message not found.');
        }

        $phone = $request->input('recipient_phone') ?: $message->recipient_phone;
        $phone = preg_replace('/\s+/', '', trim((string) $phone));
        if (!$phone) {
            return $request->wantsJson() ? response()->json(['success' => false, 'error' => 'Enter recipient phone number.'], 400) : redirect()->route('user.share-messages.page')->with('error', 'Enter recipient phone number.');
        }

        $link = $message->generated_link;
        if (!$link) {
            return $request->wantsJson() ? response()->json(['success' => false, 'error' => 'Generate a link first.'], 400) : redirect()->route('user.share-messages.page')->with('error', 'Generate a link for this message first.');
        }

        $vaultPin = null;
        if ($message->is_vaulted && $message->specific_vault_pin) {
            try {
                $vaultPin = \Illuminate\Support\Facades\Crypt::decryptString($message->getRawOriginal('specific_vault_pin'));
            } catch (\Exception $e) {}
        }

        $body = self::buildShareMessage($message->recipient_name, $link, $vaultPin);
        if (!preg_match('/^\+/', $phone)) {
            $phone = '+' . ltrim($phone, '0');
        }

        if (!config('services.twilio.sid') || !config('services.twilio.token')) {
            return $request->wantsJson() ? response()->json(['success' => false, 'error' => 'WhatsApp/Twilio is not configured.'], 400) : redirect()->route('user.share-messages.page')->with('error', 'WhatsApp is not configured.');
        }

        $twilioClientClass = 'Twilio\Rest\Client';
        if (!class_exists($twilioClientClass)) {
            return $request->wantsJson() ? response()->json(['success' => false, 'error' => 'Twilio SDK not installed.'], 400) : redirect()->route('user.share-messages.page')->with('error', 'Twilio SDK not installed.');
        }

        $customMessage = $request->input('custom_message');
        if ($customMessage) {
            $body = $customMessage;
        } else {
            $body = self::buildShareMessage($message->recipient_name ?? 'Friend', $link, $vaultPin);
        }

        $recipientHash = ShareSend::hashRecipient($phone);
        $recipientMasked = ShareSend::maskPhone($phone);

        $shareSend = ShareSend::create([
            'user_id' => Auth::id(),
            'wish_message_id' => $message->id,
            'channel' => 'whatsapp',
            'recipient_hash' => $recipientHash,
            'recipient_masked' => $recipientMasked,
            'recipient_contact' => $phone,
            'custom_message' => $customMessage,
            'status' => 'sending',
        ]);

        try {
            $client = new $twilioClientClass(
                config('services.twilio.sid'),
                config('services.twilio.token')
            );
            $client->messages->create('whatsapp:' . $phone, [
                'from' => 'whatsapp:' . config('services.twilio.from'),
                'body' => $body,
            ]);
            $shareSend->update(['status' => 'sent', 'error_message' => null]);
        } catch (\Throwable $e) {
            $shareSend->update(['status' => 'failed', 'error_message' => $e->getMessage()]);
            if ($request->wantsJson()) {
                $shareSend->load('wishMessage');
                return response()->json(['success' => false, 'error' => $e->getMessage(), 'share_send' => $this->shareSendForUi($shareSend)], 400);
            }
            return redirect()->route('user.share-messages.page')->with('error', 'Could not send WhatsApp: ' . $e->getMessage());
        }

        if ($request->wantsJson()) {
            $shareSend->load('wishMessage');
            return response()->json(['success' => true, 'share_send' => $this->shareSendForUi($shareSend)]);
        }
        return redirect()->route('user.share-messages.page')->with('success', 'WhatsApp sent successfully.');
    }

    /**
     * Send the message via all channels (Email, SMS, WhatsApp)
     */
    public function sendAll(Request $request)
    {
        $request->validate([
            'wish_message_id' => 'required|integer',
            'recipient_email' => 'required|email',
            'recipient_phone' => 'required|string|max:20',
            'custom_message' => 'nullable|string',
        ]);
        
        $results = [];
        
        // 1. Send Email
        $emailRequest = $request->duplicate();
        $emailResponse = $this->sendEmail($emailRequest);
        $results['email'] = $emailResponse instanceof \Illuminate\Http\JsonResponse ? $emailResponse->getData(true) : ['success' => true];
        
        // 2. Send SMS
        $smsRequest = $request->duplicate();
        $smsResponse = $this->sendSms($smsRequest);
        $results['sms'] = $smsResponse instanceof \Illuminate\Http\JsonResponse ? $smsResponse->getData(true) : ['success' => true];
        
        // 3. Send WhatsApp
        $waRequest = $request->duplicate();
        $waResponse = $this->sendWhatsapp($waRequest);
        $results['whatsapp'] = $waResponse instanceof \Illuminate\Http\JsonResponse ? $waResponse->getData(true) : ['success' => true];

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'results' => $results]);
        }
        
        return redirect()->route('user.share-messages.page')->with('success', 'Message sent via all channels.');
    }

    /**
     * Schedule a future share send (does not execute immediately).
     */
    public function schedulePage(Request $request)
    {
        $user = Auth::user();
        $userId = $user->id;

        $templateList = TemplateController::getAllTemplateKeys();
        $notifications = \App\Models\UserNotification::forUser($userId)->orderBy('created_at', 'desc')->limit(50)->get();
        $messagesWithLink = WishMessages::where('user_id', $userId)->whereHas('generatedLinks')->get();
        $recentShareSends = ShareSend::where('user_id', $userId)->with('wishMessage')->latest()->take(5)->get();
        $selectedMessageId = session('share_messages_id');

        return view('user.pages.messages.share-messages-schedule', compact('user', 'templateList', 'notifications', 'messagesWithLink', 'recentShareSends', 'selectedMessageId'));
    }

    public function schedule(Request $request)
    {
        $request->validate([
            'wish_message_id' => 'required|integer',
            'channel' => 'required|in:email,sms,whatsapp,all',
            'recipient_email' => 'required_if:channel,email,all|nullable|email',
            'recipient_phone' => 'required_if:channel,sms,whatsapp,all|nullable|string|max:20',
            'custom_message' => 'nullable|string',
            'scheduled_date' => 'required|date|after_or_equal:today',
            'scheduled_time' => 'required|date_format:H:i',
        ]);

        $message = WishMessages::where('user_id', Auth::id())->find($request->input('wish_message_id'));
        if (!$message) {
            return redirect()->route('user.share-messages.page')
                ->with('error', 'Message not found.');
        }

        $link = $message->generated_link;
        if (!$link) {
            return redirect()->route('user.share-messages.page')
                ->with('error', 'Generate a link for this message first.');
        }

        $scheduledAt = \Carbon\Carbon::parse($request->input('scheduled_date') . ' ' . $request->input('scheduled_time'));
        if ($scheduledAt->isPast()) {
            return $request->wantsJson() ? response()->json(['success' => false, 'error' => 'Scheduled time must be in the future.']) : redirect()->back()->with('error', 'Scheduled time must be in the future.');
        }

        $channel = $request->input('channel');
        $channels = $channel === 'all' ? ['email', 'sms', 'whatsapp'] : [$channel];
        $shareSends = [];

        foreach ($channels as $c) {
            $recipient = $c === 'email' ? $request->input('recipient_email') : $request->input('recipient_phone');
            if (!$recipient) continue;

            $recipientHash = ShareSend::hashRecipient($recipient);
            $recipientMasked = $c === 'email' ? ShareSend::maskEmail($recipient) : ShareSend::maskPhone($recipient);

            $shareSends[] = ShareSend::create([
                'user_id' => Auth::id(),
                'wish_message_id' => $message->id,
                'channel' => $c,
                'recipient_hash' => $recipientHash,
                'recipient_masked' => $recipientMasked,
                'recipient_contact' => $recipient,
                'custom_message' => $request->input('custom_message'),
                'status' => 'scheduled',
                'scheduled_at' => $scheduledAt,
            ]);
        }

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'share_sends' => array_map(fn($s) => $this->shareSendForUi($s), $shareSends)]);
        }

        return redirect()->route('user.share-messages.page')
            ->with('success', 'Message scheduled successfully.')
            ->with('success_context', 'share_messages_schedule');
    }
}

