<?php

namespace App\Http\Controllers;

use App\Models\UserNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserNotificationsController extends Controller
{
    /**
     * Return the current user's notifications for the notification page.
     */
    public function index(Request $request)
    {
        $allowedContexts = [
            'admin_message',
            'message_viewed',
            'account_created',
            'message_created',
            'media_created',
            'link_generated',
            'message_shared'
        ];

        $userId = Auth::id();
        $notifications = UserNotification::where(function ($query) use ($userId) {
                $query->where('user_id', $userId)
                      ->orWhere('sender_id', $userId);
            })
            ->whereIn('context', $allowedContexts)
            ->rootThreads()
            ->with(['sender', 'replies.sender'])
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get();

        // Format to include is_from_admin flag and display name
        $formatted = $notifications->map(function ($notification) {
            $data = $notification->toArray();
            $data['is_from_admin'] = $notification->isFromAdmin();
            $data['sender_name'] = $notification->display_sender_name;
            
            if (isset($data['replies'])) {
                $data['replies'] = $notification->replies->map(function ($reply) {
                    $replyData = $reply->toArray();
                    $replyData['is_from_admin'] = $reply->isFromAdmin();
                    $replyData['sender_name'] = $reply->display_sender_name;
                    return $replyData;
                })->toArray();
            }
            
            return $data;
        });

        return response()->json($formatted);
    }

    /**
     * Mark a single notification as read.
     */
    public function markAsRead(Request $request, $id)
    {
        $userId = Auth::id();
        $notification = UserNotification::where(function ($query) use ($userId) {
            $query->where('user_id', $userId)->orWhere('sender_id', $userId);
        })->findOrFail($id);
        $notification->update(['read_at' => now()]);
        return response()->json(['ok' => true]);
    }

    /**
     * Mark all notifications as read for the current user.
     */
    public function markAllAsRead(Request $request)
    {
        UserNotification::forUser(Auth::id())->unread()->update(['read_at' => now()]);
        return response()->json(['ok' => true]);
    }

    /**
     * Compose a new message to the admin.
     */
    public function composeToAdmin(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:2000'
        ]);

        $admin = \App\Models\User::where('role', 'admin')->first();
        if (!$admin) {
            return response()->json(['error' => 'Admin not found'], 404);
        }

        $notification = UserNotification::create([
            'user_id' => $admin->id,
            'sender_id' => Auth::id(),
            'type' => UserNotification::TYPE_INFO,
            'title' => 'Message from ' . Auth::user()->name,
            'message' => $request->message,
            'context' => 'admin_message', // Using admin_message context for all direct messaging
        ]);

        return response()->json(['ok' => true, 'notification' => $notification]);
    }

    /**
     * Reply to an existing admin message.
     */
    public function replyToAdmin(Request $request, $id)
    {
        $request->validate([
            'message' => 'required|string|max:2000'
        ]);

        // Find the notification the user is replying to
        $parentMessage = UserNotification::forUser(Auth::id())->findOrFail($id);

        if (!$parentMessage->isFromAdmin()) {
            return response()->json(['error' => 'You can only reply to admin messages.'], 403);
        }

        $admin = \App\Models\User::where('role', 'admin')->first();
        
        $reply = UserNotification::create([
            'user_id' => $admin->id,
            'sender_id' => Auth::id(),
            'parent_id' => $parentMessage->id,
            'root_id' => $parentMessage->root_id ?? $parentMessage->id,
            'type' => UserNotification::TYPE_INFO,
            'title' => 'Reply from ' . Auth::user()->name,
            'message' => $request->message,
            'context' => 'admin_message',
            'is_reply' => true,
        ]);

        $parentMessage->update(['replied_at' => now()]);

        return response()->json(['ok' => true, 'notification' => $reply]);
    }

    /**
     * Delete a notification.
     */
    public function destroy(Request $request, $id)
    {
        $userId = Auth::id();
        $notification = UserNotification::where(function ($query) use ($userId) {
            $query->where('user_id', $userId)->orWhere('sender_id', $userId);
        })->findOrFail($id);
        $notification->delete();
        return response()->json(['ok' => true]);
    }
}
