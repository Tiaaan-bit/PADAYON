<?php
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Symfony\Component\HttpFoundation\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(web: __DIR__ . '/../routes/web.php', commands: __DIR__ . '/../routes/console.php', health: '/up')
    ->withMiddleware(function (Middleware $middleware) {

    $middleware->trustProxies(at: '*', headers: Request::HEADER_X_FORWARDED_FOR | Request::HEADER_X_FORWARDED_HOST | Request::HEADER_X_FORWARDED_PORT | Request::HEADER_X_FORWARDED_PROTO);

        $middleware->validateCsrfTokens(except: ['webhooks/paymongo']);

        $middleware->append(\App\Http\Middleware\TrackOnlineStatus::class);

        $middleware->alias([
            'auth.user' => \App\Http\Middleware\AuthUser::class,
            'auth.admin' => \App\Http\Middleware\AuthAdmin::class,
            'auth.therapist' => \App\Http\Middleware\AuthTherapist::class,
            'auth.staff' => \App\Http\Middleware\AuthStaff::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })
    ->create();
