<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use App\Models\Subscription;

class EnsureSubscriptionIsActive
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $enabled = \Illuminate\Support\Facades\DB::table('system_settings')->where('key', 'payment_system_enabled')->value('value') === '1';

        if ($enabled && Auth::check() && Auth::user()->role !== 'admin') {
            $hasActive = Subscription::where('user_id', Auth::id())
                ->where('status', 'active')
                ->where('expires_at', '>', now())
                ->exists();

            if (!$hasActive && !$request->routeIs('user.billing.*')) {
                return redirect()->route('user.billing.index')->with('warning', 'Please subscribe to continue using premium features.');
            }
        }

        return $next($request);
    }
}
