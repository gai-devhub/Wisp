<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class ProfilePictureController extends Controller
{
    /**
     * Serve the current user's profile picture.
     * Proxies external URLs (e.g. Google) so the image loads even when the provider blocks hotlinking.
     */
    public function show(Request $request)
    {
        $user = Auth::user();
        if (! $user) {
            abort(404);
        }

        $url = $user->profile_picture ?? '';

        if (empty($url)) {
            return redirect()->away(
                'https://ui-avatars.com/api/?name=' . urlencode($user->username ?? 'User') . '&color=7F9CF5&background=EBF4FF'
            );
        }

        $url = trim($url);

        // External URL (e.g. Google) – proxy it so the image loads
        if (str_starts_with($url, 'http://') || str_starts_with($url, 'https://')) {
            try {
                $response = Http::timeout(10)
                    ->withOptions(['allow_redirects' => true])
                    ->withHeaders(['User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; rv:91.0) Gecko/20100101 Firefox/91.0'])
                    ->get($url);
                if ($response->successful()) {
                    $contentType = $response->header('Content-Type') ?: 'image/jpeg';
                    return response($response->body(), 200, [
                        'Content-Type' => $contentType,
                        'Cache-Control' => 'private, max-age=3600',
                    ]);
                }
            } catch (\Throwable $e) {
                // Fall through to fallback
            }
            return redirect()->away(
                'https://ui-avatars.com/api/?name=' . urlencode($user->username ?? 'User') . '&color=7F9CF5&background=EBF4FF'
            );
        }

        // S3 path – redirect to S3 URL
        return redirect()->away(\Illuminate\Support\Facades\Storage::disk('s3')->url($url));
    }
}
