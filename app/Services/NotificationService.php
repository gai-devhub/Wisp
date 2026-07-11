<?php

namespace App\Services;

use App\Models\UserNotification;

class NotificationService
{
    /**
     * Record an action/event for the user (success, error, info, warning).
     *
     * @param int $userId
     * @param string $type One of: info, success, warning, error
     * @param string $title
     * @param string $message
     * @param array $meta Optional: action, wish_message_id, errors, etc.
     * @return UserNotification|null
     */
    public function record(int $userId, string $type, string $title, string $message, array $meta = []): ?UserNotification
    {
        try {
            return UserNotification::create([
                'user_id' => $userId,
                'type' => $type,
                'title' => $title,
                'message' => $message,
                'context' => $meta['action'] ?? null,
                'meta' => $meta,
            ]);
        } catch (\Throwable $e) {
            report($e);
            return null;
        }
    }
}
