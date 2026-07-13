<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'admin' => \App\Http\Middleware\AdminMiddleware::class,
            'user.role' => \App\Http\Middleware\EnsureUserRole::class,
            'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
            'systemOnline' => \App\Http\Middleware\SystemOnline::class,
            'require.passcode' => \App\Http\Middleware\RequirePasscode::class,
            'billing' => \App\Http\Middleware\EnsureSubscriptionIsActive::class,
        ]);
        // Apply systemOnline middleware globally
        $middleware->append(\App\Http\Middleware\SystemOnline::class);
        $middleware->web(append: [
            \App\Http\Middleware\LogUserActivity::class,
        ]);

        $middleware->redirectGuestsTo(fn () => route('auth.login'));
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (\Illuminate\Session\TokenMismatchException $e, \Illuminate\Http\Request $request) {
            if ($request->routeIs('auth.logout')) {
                return redirect()->route('auth.login');
            }
            if ($request->expectsJson() || $request->ajax() || $request->isXmlHttpRequest() || in_array($request->method(), ['POST', 'PUT', 'DELETE', 'PATCH'])) {
                return response()->json([
                    'message' => 'Your session has expired. Please reload the page or log in again.',
                    'redirect' => route('auth.login')
                ], 419);
            }
            return redirect()->route('auth.login')->with('error', 'Your session has expired. Please try again.');
        });

        $exceptions->render(function (\Illuminate\Auth\AuthenticationException $e, \Illuminate\Http\Request $request) {
            if ($request->expectsJson() || $request->ajax() || $request->isXmlHttpRequest() || in_array($request->method(), ['POST', 'PUT', 'DELETE', 'PATCH'])) {
                return response()->json([
                    'message' => 'Your session has expired. Please reload the page or log in again.',
                    'redirect' => route('auth.login')
                ], 401);
            }
            return redirect()->guest(route('auth.login'))->with('error', 'Please log in to continue.');
        });
    })->create();
