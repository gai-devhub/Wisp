<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\WishMessages;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class WishMessagesController extends Controller
{
    /** Validation rules for storing a new message (form field names). */
    protected function storeRules(): array
    {
        return [
            'message_type' => ['required', 'string', 'max:30', 'regex:/^[A-Za-z\s]+$/'],
            'page_title' => 'required|string|max:255',
            'recipient_full_name' => 'required|string|max:255',
            'recipient_name' => 'required|string|max:255',
            'greeting' => 'required|string|max:255',
            'wish_message' => 'required|string',
            'last_note' => 'required|string|max:255',
            'receiving_date' => 'required|date',
            'sender_name' => 'required|string|max:255',
            'is_vaulted' => 'nullable|boolean',
            'specific_vault_pin' => 'nullable|string|max:255',
            'is_published' => 'nullable|boolean',
        ];
    }

    /** Validation rules for updating (same fields, all optional except we validate what's sent). */
    protected function editRules(): array
    {
        return [
            'message_type' => ['sometimes', 'string', 'max:30', 'regex:/^[A-Za-z\s]+$/'],
            'page_title' => 'sometimes|string|max:255',
            'recipient_full_name' => 'sometimes|string|max:255',
            'recipient_name' => 'sometimes|string|max:255',
            'greeting' => 'sometimes|string|max:255',
            'wish_message' => 'sometimes|string',
            'last_note' => 'sometimes|string|max:255',
            'receiving_date' => 'sometimes|date',
            'sender_name' => 'sometimes|string|max:255',
            'is_vaulted' => 'nullable|boolean',
            'specific_vault_pin' => 'nullable|string|max:255',
            'is_published' => 'nullable|boolean',
        ];
    }

    /**
     * Return the current user's messages for dropdowns (JSON).
     */
    public function index(Request $request)
    {
        $messages = WishMessages::forUser()
            ->orderBy('created_at', 'desc')
            ->get(['id', 'title', 'recipient_name', 'recipient_special_name', 'created_at']);

        return response()->json($messages);
    }

    /**
     * Get a single message for the current user (for editing / display). Returns 404 if not found or not owner.
     */
    public function show(Request $request, $id)
    {
        $message = WishMessages::forUser()->findOrFail($id);
        return response()->json($message);
    }

    /**
     * Store a new message for the authenticated user.
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        // Free user message limit: max 5 messages
        if (!$user->isPremium()) {
            $messageCount = $user->freeMessageCount();
            if ($messageCount >= 5) {
                if ($request->wantsJson()) {
                    return response()->json([
                        'message' => "You've used all {$messageCount} of your free messages. Upgrade to Premium for unlimited messages!",
                        'upgrade_required' => true
                    ], 403);
                }
                return redirect()->route('user.billing.index')
                    ->with('upgrade_prompt', true)
                    ->with('upgrade_message', "You've used all {$messageCount} of your free messages. Upgrade to Premium for unlimited messages!");
            }
        }

        if ($user->isStorageFull()) {
            if ($request->wantsJson()) {
                return response()->json([
                    'message' => 'Your storage is full (1GB limit reached). Please delete some old media files or messages to free up space.'
                ], 403);
            }
            return redirect()->route('user.my-messages.page')
                ->with('error', 'Your storage is full (1GB limit reached). Please delete some old media files or messages to free up space.')
                ->withInput();
        }

        try {
            $valid = $request->validate($this->storeRules());
        } catch (ValidationException $e) {
            app(\App\Services\NotificationService::class)->record(
                Auth::id(),
                'error',
                'Message save failed',
                $e->getMessage(),
                ['action' => 'messages.store', 'errors' => $e->errors()]
            );
            throw $e;
        }

        $message = new WishMessages();
        $message->user_id = Auth::id();
        $message->message_type = $valid['message_type'];
        $message->title = $valid['page_title'];
        $message->recipient_name = $valid['recipient_full_name'];
        $message->recipient_special_name = $valid['recipient_name'];
        $message->greeting = $valid['greeting'];
        $message->message = $valid['wish_message'];
        $message->last_note = $valid['last_note'];
        $message->receiving_date = $valid['receiving_date'];
        $message->sender_name = $valid['sender_name'];
        $message->is_vaulted = $request->has('is_vaulted');
        if ($message->is_vaulted && !empty($valid['specific_vault_pin'])) {
            $message->specific_vault_pin = Crypt::encryptString($valid['specific_vault_pin']);
        }

        $settings = \App\Models\UserSettings::firstOrCreate(
            ['user_id' => Auth::id()],
            ['page_expiry' => 24, 'page_view_alerts' => true, 'whatsapp_notifications' => true, 'auto_delete_expired' => 'never']
        );
        $message->expiry_hours = $settings->page_expiry ?? 24;
        $message->is_published = false;
        
        $rd = $message->receiving_date;
        if ($rd) {
            $message->expires_at = \Carbon\Carbon::parse($rd instanceof \DateTimeInterface ? $rd : (string) $rd)->setTime(now()->hour, now()->minute, now()->second)->addHours((int) $message->expiry_hours);
        }
        
        $message->save();

        if (class_exists(ActivityLog::class) && \Illuminate\Support\Facades\Schema::hasTable('activity_log')) {
            ActivityLog::log(Auth::id(), 'Message created', '"' . ($message->title ?? '') . '" for ' . ($message->recipient_name ?? '—'));
        }

        app(\App\Services\NotificationService::class)->record(
            Auth::id(),
            'success',
            'Message saved',
            'Message saved successfully.',
            ['action' => 'messages.store', 'wish_message_id' => $message->id]
        );

        if ($request->wantsJson()) {
            return response()->json($message, 201);
        }
        return redirect()->route('user.my-messages.page')
            ->with('success', 'Message saved successfully.');
    }

    /**
     * Update an existing message. Only the owner can update.
     */
    public function update(Request $request, $id)
    {
        $message = WishMessages::forUser()->findOrFail($id);
        
        $oldTitle = $message->title;
        $oldRecipient = $message->recipient_name;
        $oldMessageContent = $message->message;

        $valid = $request->validate($this->editRules());

        if (isset($valid['message_type'])) {
            $message->message_type = $valid['message_type'];
        }
        if (array_key_exists('page_title', $valid)) {
            $message->title = $valid['page_title'];
        }
        if (isset($valid['recipient_full_name'])) {
            $message->recipient_name = $valid['recipient_full_name'];
        }
        if (isset($valid['recipient_name'])) {
            $message->recipient_special_name = $valid['recipient_name'];
        }
        if (isset($valid['greeting'])) {
            $message->greeting = $valid['greeting'];
        }
        if (array_key_exists('wish_message', $valid)) {
            $message->message = $valid['wish_message'];
        }
        if (isset($valid['last_note'])) {
            $message->last_note = $valid['last_note'];
        }
        if (isset($valid['receiving_date'])) {
            $message->receiving_date = $valid['receiving_date'];
        }
        if (isset($valid['sender_name'])) {
            $message->sender_name = $valid['sender_name'];
        }
        $message->is_vaulted = $request->has('is_vaulted');
        if ($message->is_vaulted) {
            if (!empty($valid['specific_vault_pin']) && $valid['specific_vault_pin'] !== $message->specific_vault_pin) {
                $message->specific_vault_pin = Crypt::encryptString($valid['specific_vault_pin']);
            }
        } else {
            $message->specific_vault_pin = null;
        }

        if ($request->has('is_published')) {
            $message->is_published = $request->boolean('is_published');
        }

        // Recompute expires_at from receiving_date + expiry_hours when receiving_date is set
        $settings = \App\Models\UserSettings::firstOrCreate(
            ['user_id' => Auth::id()],
            ['page_expiry' => 24, 'page_view_alerts' => true, 'whatsapp_notifications' => true, 'auto_delete_expired' => 'never']
        );
        $message->expiry_hours = $settings->page_expiry ?? $message->expiry_hours ?? 24;
        $rd = $message->receiving_date;
        if ($rd) {
            $message->expires_at = \Carbon\Carbon::parse($rd instanceof \DateTimeInterface ? $rd : (string) $rd)->setTime(now()->hour, now()->minute, now()->second)->addHours((int) $message->expiry_hours);
        }

        $message->save();

        $changes = [];
        if ($oldTitle !== $message->title) {
            $changes[] = 'Title updated from "' . ($oldTitle ?? 'Empty') . '" to "' . ($message->title ?? 'Empty') . '"';
        }
        if ($oldRecipient !== $message->recipient_name) {
            $changes[] = 'Recipient updated from "' . ($oldRecipient ?? 'Empty') . '" to "' . ($message->recipient_name ?? 'Empty') . '"';
        }
        if ($oldMessageContent !== $message->message) {
            $oldTruncated = strlen($oldMessageContent) > 30 ? substr($oldMessageContent, 0, 30) . '...' : $oldMessageContent;
            $newTruncated = strlen($message->message) > 30 ? substr($message->message, 0, 30) . '...' : $message->message;
            $changes[] = 'Message content changed from "' . ($oldTruncated ?? 'Empty') . '" to "' . ($newTruncated ?? 'Empty') . '"';
        }

        if (!empty($changes)) {
            $user = Auth::user();
            \App\Models\UserNotification::create([
                'user_id' => $user->id,
                'type' => \App\Models\UserNotification::TYPE_INFO,
                'title' => 'Message updated: ' . (strlen($message->title) > 15 ? substr($message->title, 0, 15) . '...' : $message->title),
                'message' => implode("\n", $changes),
            ]);

            try {
                \Illuminate\Support\Facades\Mail::to($user->email)->send(new \App\Mail\MessageUpdatedMail($user, $message, $changes));
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Failed to send message updated email: ' . $e->getMessage());
            }
        }

        if ($request->wantsJson()) {
            return response()->json($message);
        }
        return redirect()->route('user.my-messages.page')
            ->with('success', 'Message updated successfully.');
    }

    /**
     * Delete a message (Soft Delete).
     */
    public function destroy(Request $request, $id)
    {
        $message = WishMessages::forUser()->findOrFail($id);
        $message->delete();

        if ($request->wantsJson()) {
            return response()->json(['deleted' => true]);
        }
        return redirect()->route('user.my-messages.page')
            ->with('success', 'Message sent to trash.');
    }

    /**
     * Permanently delete a message from the trash.
     */
    public function forceDestroy(Request $request, $id)
    {
        $message = WishMessages::forUser()->onlyTrashed()->findOrFail($id);
        
        // Explicitly delete associated records
        $message->generatedLinks()->delete();
        $message->mediaFiles()->delete();
        $message->views()->delete();
        $message->shareSends()->delete();
        $message->template()->delete();

        // Wipe out the vault pin
        $message->specific_vault_pin = null;
        $message->is_vaulted = false;
        $message->save();

        // Completely remove the message
        $message->forceDelete();

        if ($request->wantsJson()) {
            return response()->json(['deleted' => true]);
        }
        return redirect()->route('user.trash.page')
            ->with('success', 'Message permanently deleted.');
    }

    /**
     * Restore a soft-deleted message from the trash.
     */
    public function restoreMessage(Request $request, $id)
    {
        $message = WishMessages::forUser()->onlyTrashed()->findOrFail($id);
        $message->restore();

        if ($request->wantsJson()) {
            return response()->json(['restored' => true]);
        }
        return redirect()->route('user.trash.page')
            ->with('success', 'Message restored.');
    }
    /**
     * Generate a shareable link for a message.
     */
    public function shareMessage(Request $request, $id)
    {
        $message = WishMessages::forUser()->findOrFail($id);

        // Check if an active link already exists
        $existingLink = $message->generatedLinks()->where('is_active', true)->latest()->first();
        
        if ($existingLink) {
            return response()->json(['link' => $existingLink->generated_url]);
        }

        // Generate a new unique code and URL
        $uniqueCode = \Illuminate\Support\Str::random(10);
        $url = config('app.url') . '/view/message/' . \Illuminate\Support\Str::slug($message->message_type) . '/' . $message->slug;

        $link = new \App\Models\GeneratedLinks();
        $link->wish_message_id = $message->id;
        $link->unique_code = $uniqueCode;
        $link->generated_url = $url;
        $link->is_active = true;
        // Optionally set expires_at based on message's expires_at
        if ($message->expires_at) {
            $link->expires_at = $message->expires_at;
        }
        $link->save();

        return response()->json(['link' => $link->generated_url]);
    }
}
