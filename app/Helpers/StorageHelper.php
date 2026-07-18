<?php

use Illuminate\Support\Facades\Storage;

if (!function_exists('media_disk')) {
    /**
     * Disk used for user-uploaded media (images, audio, profile pictures, ads).
     * Production: s3. Local dev: public.
     */
    function media_disk(): string
    {
        return config('filesystems.media_disk', 'public');
    }
}

if (!function_exists('profile_picture_storage_path')) {
    /**
     * Normalize a profile picture DB value to its storage path.
     */
    function profile_picture_storage_path(string $stored): string
    {
        if (str_starts_with($stored, 'http://') || str_starts_with($stored, 'https://')) {
            return $stored;
        }

        if (! str_contains($stored, '/')) {
            return 'profile-pictures/' . $stored;
        }

        return $stored;
    }
}

if (!function_exists('delete_storage_file')) {
    /**
     * Delete a file from the configured media disk when it is a stored path.
     */
    function delete_storage_file(?string $path): void
    {
        if (empty($path) || str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return;
        }

        try {
            Storage::disk(media_disk())->delete($path);
        } catch (\Throwable $e) {
            // Ignore missing files or misconfigured disks during cleanup.
        }
    }
}

if (!function_exists('s3_url')) {
    /**
     * Return a public URL for a file stored on the media disk (S3 in production).
     * If the path is already a full URL (e.g. Google/Spotify), it is returned as-is.
     */
    function s3_url(?string $path): string
    {
        if (empty($path)) {
            return '';
        }

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        try {
            return Storage::disk(media_disk())->url($path);
        } catch (\Throwable $e) {
            try {
                return Storage::disk('public')->url($path);
            } catch (\Throwable $e) {
                return '';
            }
        }
    }
}

if (!function_exists('media_url')) {
    /**
     * Unified URL resolver for media paths or external URLs.
     */
    function media_url(?string $path): string
    {
        if (empty($path)) {
            return '';
        }

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        return s3_url($path);
    }
}
