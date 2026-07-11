<?php

namespace App\Http\Controllers;

use App\Models\MessageViews;
use App\Models\Template;
use App\Models\UserNotification;
use App\Models\UserSettings;
use App\Models\WishMessages;
use Carbon\Carbon;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Hash;

class MessageViewController extends Controller
{
    /**
     * Show the public message page at /{type}/{slug}.
     * Only works if the user has generated a link for this message.
     */
    public function show(string $type, string $slug)
    {
        $message = WishMessages::where('message_type', $type)
            ->where('slug', $slug)
            ->first();

        if (!$message) {
            return response()->view('sub-folder.wrong-link', [], 404);
        }

        $hasActiveLink = $message->generatedLinks()
            ->where('is_active', true)
            ->exists();

        if (!$hasActiveLink) {
            return response()->view('sub-folder.wrong-link', [], 404);
        }

        // Not yet the receiving day: link does not work until receiving date
        if ($message->receiving_date) {
            $receivingStart = Carbon::parse($message->receiving_date)->startOfDay();
            if (now()->lt($receivingStart)) {
                $receivingDateFormatted = $receivingStart->format('l, F j, Y');
                $recipientImageUrl = null;
                $media = $message->mediaFiles;
                if ($media && $media->recipient_image) {
                    $recipientImageUrl = asset('storage/' . $media->recipient_image);
                }
                return response()->view('sub-folder.message-not-available', [
                    'receiving_date'       => $receivingDateFormatted,
                    'recipient_image_url' => $recipientImageUrl,
                ], 403);
            }
        }

        if ($message->expires_at && $message->expires_at->isPast()) {
            $recipientImageUrl = null;
            $media = $message->mediaFiles;
            if ($media && $media->recipient_image) {
                $recipientImageUrl = asset('storage/' . $media->recipient_image);
            }
            return response()->view('sub-folder.message-expired', [
                'recipient_image_url' => $recipientImageUrl,
            ], 410);
        }

        // Require viewer consent before showing the message
        $consentKey = 'viewer_consent_' . $message->slug;
        if (!session($consentKey)) {
            $creatorName = $message->sender_name
                ?: $message->user->name
                ?? $message->user->username
                ?? 'Someone';
            $messageTypeDisplay = ucfirst(str_replace('_', ' ', $message->message_type ?? ''));
            return response()->view('components.viewer-concent', [
                'type'                 => $type,
                'slug'                 => $message->slug,
                'pageTitle'            => $message->title ?? 'View message',
                'creatorName'          => $creatorName,
                'messageTypeDisplay'   => $messageTypeDisplay,
                'isVaulted'            => $message->is_vaulted,
            ]);
        }

        $template = Template::where('wish_message_id', $message->id)->first();
        $templateName = $template ? $template->template_name : 'template.view.template-1';
        
        $viewName = null;
        if (preg_match('/^view-([1-6])$/', $templateName, $m) || preg_match('/^view\.view-([1-6])$/', $templateName, $m) || preg_match('/^template\.view\.view-([1-6])$/', $templateName, $m)) {
            $viewName = 'components.template.view.template-' . $m[1];
        } elseif (preg_match('/^(aurora|casual|confetti|minimal|garden|glitter|romance)-(\d+)$/i', $templateName, $m)) {
            $theme = ucfirst(strtolower($m[1]));
            $num = (int) $m[2];
            $viewName = 'components.template.' . $theme . '.template-' . $num;
        } else {
            $viewName = 'components.' . $templateName;
        }

        if (!$viewName || !View::exists($viewName)) {
            $viewName = 'components.template.view.template-1';
        }

        $message->load('mediaFiles');
        $mediaFiles = $message->mediaFiles;

        // Record view when someone opens the public link
        try {
            MessageViews::create([
                'user_id'         => $message->user_id,
                'wish_message_id' => $message->id,
                'date'            => now()->toDateString(),
                'ip_address'      => request()->ip(),
                'viewed_at'       => now(),
            ]);
            // Notify message owner only if they have page view alerts enabled
            $settings = UserSettings::where('user_id', $message->user_id)->first();
            if ($settings && $settings->page_view_alerts) {
                $owner = \App\Models\User::find($message->user_id);
                $messageTitle = $message->title ?? $message->recipient_name;
                
                UserNotification::create([
                    'user_id' => $message->user_id,
                    'type'    => UserNotification::TYPE_INFO,
                    'title'   => 'Page viewed',
                    'message' => 'Someone viewed your message: ' . $messageTitle,
                    'meta'    => ['wish_message_id' => $message->id],
                ]);

                if ($owner) {
                    \Illuminate\Support\Facades\Mail::to($owner->email)->send(new \App\Mail\PageViewAlert($owner, $messageTitle));
                }
            }
        } catch (\Throwable $e) {
            // Don't block the page if recording fails
            report($e);
        }

        return view($viewName, [
            'message'    => $message,
            'mediaFiles' => $mediaFiles,
        ]);
    }

    /**
     * Accept viewer consent and redirect to the message.
     */
    public function acceptConsent(\Illuminate\Http\Request $request, string $type, string $slug)
    {
        $message = WishMessages::where('message_type', $type)
            ->where('slug', $slug)
            ->first();

        if (!$message) {
            abort(404);
        }

        $hasActiveLink = $message->generatedLinks()
            ->where('is_active', true)
            ->exists();

        if (!$hasActiveLink) {
            abort(404);
        }

        if ($message->receiving_date) {
            $receivingStart = Carbon::parse($message->receiving_date)->startOfDay();
            if (now()->lt($receivingStart)) {
                abort(403);
            }
        }

        if ($message->expires_at && $message->expires_at->isPast()) {
            abort(410);
        }

        if ($message->is_vaulted) {
            $isCorrect = false;
            if ($message->specific_vault_pin) {
                try {
                    $decrypted = \Illuminate\Support\Facades\Crypt::decryptString($message->getRawOriginal('specific_vault_pin'));
                    $isCorrect = ($decrypted === $request->input('vault_pin'));
                } catch (\Exception $e) {
                    $isCorrect = false;
                }
            } else {
                $settings = UserSettings::where('user_id', $message->user_id)->first();
                if ($settings && Hash::check($request->input('vault_pin'), $settings->vault_pin)) {
                    $isCorrect = true;
                }
            }

            if (!$isCorrect) {
                return redirect()->back()->with('error', 'Incorrect Vault PIN. Please try again.');
            }
        }

        session(['viewer_consent_' . $message->slug => true]);

        return redirect()->route('message.show', ['type' => $type, 'slug' => $slug]);
    }
}
