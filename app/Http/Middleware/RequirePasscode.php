<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RequirePasscode
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            $user = Auth::user();
            
            // Allow them to logout even if locked out
            if ($request->routeIs('auth.logout')) {
                return $next($request);
            }

            // Check if user has settings and a passcode
            $settings = $user->settings;
            if ($settings && !empty($settings->login_passcode)) {
                // If they haven't verified it in this session, redirect to verification
                if ($request->session()->get('passcode_verified', false) === false) {
                    
                    // Prevent redirect loops
                    if (!$request->routeIs('auth.passcode.verify') && !$request->routeIs('auth.passcode.verify.post')) {
                        return redirect()->route('auth.passcode.verify');
                    }
                }
            }
        }

        return $next($request);
    }
}
