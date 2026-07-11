<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class SystemOnline
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $online = (bool) Cache::rememberForever('system_online', fn () => true);
        if ($online) {
            return $next($request);
        }

        // Allow admin and auth entry points when offline
        $path = '/'.ltrim($request->path(), '/');
        $allowedPrefixes = [
            '/admin',
        ];
        $allowedExact = [
            '/',
            '/login',
            '/auth/google',
            '/auth/google/callback',
        ];

        foreach ($allowedPrefixes as $prefix) {
            if (str_starts_with($path, $prefix)) {
                return $next($request);
            }
        }
        if (in_array($path, $allowedExact, true)) {
            return $next($request);
        }

        return response()->view('maintenance', [], 503);
    }
}


