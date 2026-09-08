<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    
    public function boot(): void
    {
        RateLimiter::for('login', function (Request $request) {
            return Limit::perMinutes(10, 5)
                ->by(strtolower($request->input('email')) . '|' . $request->ip())
                ->response(function (Request $request, array $headers) {
                    $retryAfter = $headers['Retry-After'] ?? 600;

                    return back()
                        ->withErrors([
                            'email' => 'Too many login attempts, Try again later.',
                        ])
                        ->with('login_rate_limited', true)
                        ->with('retry_after', now()->addSeconds((int) $retryAfter)->timestamp)
                        ->withInput();
                });
        });
    }
}
