<?php

use App\Http\Middleware\EnsureIsAdmin;
use App\Http\Middleware\EnsureIsCustomer;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'admin' => EnsureIsAdmin::class,
            'customer' => EnsureIsCustomer::class,
        ]);

        // Redirect unauthenticated users to the login page that matches the area
        // they were trying to reach: admin/* paths go to the admin login, any
        // other guarded path (e.g. the customer profile) goes to the customer login.
        $middleware->redirectGuestsTo(function (Request $request) {
            return $request->is('admin/*') ? route('admin.login') : route('user.login');
        });

        // Send already-authenticated users to the storefront home instead of the
        // default /home when they hit a guest-only page (e.g. /login or /register).
        $middleware->redirectUsersTo(fn () => route('home'));
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );
    })->create();
