<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LogUserActivity
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check()) {
            // Ignore certain paths to avoid cluttering logs
            $ignoredPaths = ['_debugbar*', 'build/*', 'storage/*', 'admin/api/*'];
            
            if (!$request->is(...$ignoredPaths) && !$request->ajax()) {
                $method = $request->method();
                $path = $request->path();
                
                if ($method === 'GET') {
                    \App\Models\ActivityLog::log(
                        auth()->id(), 
                        'PAGE VISITED', 
                        "Visited: /" . $path
                    );
                } else {
                    \App\Models\ActivityLog::log(
                        auth()->id(), 
                        'ACTION PERFORMED', 
                        "Method: {$method} | Path: /{$path}"
                    );
                }
            }
        }

        return $next($request);
    }
}
