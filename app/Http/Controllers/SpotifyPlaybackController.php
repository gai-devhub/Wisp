<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class SpotifyPlaybackController extends Controller
{
    private const SESSION_RETURN = 'spotify_return_url';
    private const SESSION_VERIFIER = 'spotify_code_verifier';
    private const SESSION_ACCESS = 'spotify_access_token';
    private const SESSION_REFRESH = 'spotify_refresh_token';
    private const SESSION_EXPIRES = 'spotify_token_expires_at';

    public function connect(Request $request)
    {
        $returnUrl = $request->query('return_url', url()->previous() ?: url('/'));

        if (!$this->isAllowedReturnUrl($returnUrl)) {
            $returnUrl = url('/');
        }

        $verifier = Str::random(64);
        $challenge = rtrim(strtr(base64_encode(hash('sha256', $verifier, true)), '+/', '-_'), '=');

        session([
            self::SESSION_RETURN => $returnUrl,
            self::SESSION_VERIFIER => $verifier,
        ]);

        $query = http_build_query([
            'client_id' => config('spotify.auth.client_id'),
            'response_type' => 'code',
            'redirect_uri' => $this->redirectUri(),
            'scope' => 'streaming user-read-email user-read-private user-modify-playback-state',
            'code_challenge_method' => 'S256',
            'code_challenge' => $challenge,
        ]);

        return redirect('https://accounts.spotify.com/authorize?' . $query);
    }

    public function callback(Request $request)
    {
        $returnUrl = session(self::SESSION_RETURN, url('/'));

        if ($request->filled('error')) {
            return redirect($returnUrl)->with('spotify_error', $request->input('error'));
        }

        $code = $request->input('code');
        $verifier = session(self::SESSION_VERIFIER);

        if (!$code || !$verifier) {
            return redirect($returnUrl)->with('spotify_error', 'missing_code');
        }

        try {
            $response = Http::asForm()->post('https://accounts.spotify.com/api/token', [
                'grant_type' => 'authorization_code',
                'code' => $code,
                'redirect_uri' => $this->redirectUri(),
                'client_id' => config('spotify.auth.client_id'),
                'client_secret' => config('spotify.auth.client_secret'),
                'code_verifier' => $verifier,
            ]);

            if (!$response->successful()) {
                Log::error('Spotify token exchange failed', ['body' => $response->body()]);
                return redirect($returnUrl)->with('spotify_error', 'token_exchange_failed');
            }

            $data = $response->json();
            session([
                self::SESSION_ACCESS => $data['access_token'],
                self::SESSION_REFRESH => $data['refresh_token'] ?? null,
                self::SESSION_EXPIRES => now()->addSeconds((int) ($data['expires_in'] ?? 3600)),
            ]);
        } catch (\Throwable $e) {
            Log::error('Spotify callback error: ' . $e->getMessage());
            return redirect($returnUrl)->with('spotify_error', 'exception');
        } finally {
            session()->forget([self::SESSION_VERIFIER]);
        }

        return redirect($returnUrl . (str_contains($returnUrl, '?') ? '&' : '?') . 'spotify_connected=1');
    }

    public function token(Request $request)
    {
        $token = session(self::SESSION_ACCESS);

        if (!$token) {
            return response()->json(['token' => null], 401);
        }

        if ($this->tokenExpired()) {
            if (!$this->refreshAccessToken()) {
                return response()->json(['token' => null], 401);
            }
            $token = session(self::SESSION_ACCESS);
        }

        return response()->json(['token' => $token]);
    }

    private function tokenExpired(): bool
    {
        $expiresAt = session(self::SESSION_EXPIRES);
        if (!$expiresAt) {
            return true;
        }

        return now()->greaterThanOrEqualTo($expiresAt);
    }

    private function refreshAccessToken(): bool
    {
        $refresh = session(self::SESSION_REFRESH);
        if (!$refresh) {
            return false;
        }

        try {
            $response = Http::asForm()->post('https://accounts.spotify.com/api/token', [
                'grant_type' => 'refresh_token',
                'refresh_token' => $refresh,
                'client_id' => config('spotify.auth.client_id'),
                'client_secret' => config('spotify.auth.client_secret'),
            ]);

            if (!$response->successful()) {
                session()->forget([
                    self::SESSION_ACCESS,
                    self::SESSION_REFRESH,
                    self::SESSION_EXPIRES,
                ]);
                return false;
            }

            $data = $response->json();
            session([
                self::SESSION_ACCESS => $data['access_token'],
                self::SESSION_EXPIRES => now()->addSeconds((int) ($data['expires_in'] ?? 3600)),
            ]);

            if (!empty($data['refresh_token'])) {
                session([self::SESSION_REFRESH => $data['refresh_token']]);
            }

            return true;
        } catch (\Throwable $e) {
            Log::error('Spotify refresh error: ' . $e->getMessage());
            return false;
        }
    }

    private function redirectUri(): string
    {
        return config('services.spotify.redirect_uri')
            ?? route('spotify.callback', [], true);
    }

    private function isAllowedReturnUrl(string $url): bool
    {
        $appHost = parse_url(config('app.url'), PHP_URL_HOST);
        $returnHost = parse_url($url, PHP_URL_HOST);

        return $appHost && $returnHost && $appHost === $returnHost;
    }
}
