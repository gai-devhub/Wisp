<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserRole
{
    /**
     * Allow only authenticated users with role "user". Admins are redirected to admin page.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! Auth::check()) {
            return redirect()->route('auth.login');
        }



        if (Auth::user()->status === 'blocked') {
            return redirect()->route('account.blocked');
        }

        return $next($request);
    }
}
