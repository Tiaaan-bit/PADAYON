<?php
// bootstrap/app.php  (Laravel 11 style)

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {

        // Track online status on every authenticated request
        $middleware->append(\App\Http\Middleware\TrackOnlineStatus::class);

        // Named middleware aliases
        $middleware->alias([
            'auth.user'  => \App\Http\Middleware\AuthUser::class,
            'auth.admin' => \App\Http\Middleware\AuthAdmin::class,
            'auth.therapist' => \App\Http\Middleware\AuthTherapist::class,
            'auth.staff' => \App\Http\Middleware\AuthStaff::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();