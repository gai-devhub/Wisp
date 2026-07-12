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

        return Storage::disk('s3')->url($path);
    }
}
