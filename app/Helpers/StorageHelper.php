<?php

use Illuminate\Support\Facades\Storage;

if (!function_exists('s3_url')) {
    /**
     * Return a public URL for a file stored on S3.
     * If the path is already a full URL (e.g. Google/Spotify), it is returned as-is.
     * If the path is empty, an empty string is returned.
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
            return Storage::disk('s3')->url($path);
        } catch (\Throwable $e) {
            // Fall back to public disk URL when S3 isn't available
            try {
                return Storage::disk('public')->url($path);
            } catch (\Throwable $e) {
                return '';
            }
        }
    }

    if (!function_exists('media_url')) {
        /**
         * Unified URL resolver for media paths or external URLs.
         * - If given a full URL, return as-is
         * - If empty, return empty string
         * - Otherwise try S3, then public disk as fallback
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
}
