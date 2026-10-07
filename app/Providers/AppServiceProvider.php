<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;



class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {


    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        RateLimiter::for('login', function (Request $request) {
            return Limit::perMinutes(10, 5)
                ->by(strtolower($request->input('email', '')) . '|' . $request->ip())
                ->response(function (Request $request, array $headers) {
                    $retryAfter = (int) ($headers['Retry-After'] ?? 600);

                    return back()
                        ->withErrors([
                            'email' => 'Too many login attempts. Please try again later.',
                        ])
                        ->with('login_rate_limited', true)
                        ->with('retry_after', now()->addSeconds($retryAfter)->timestamp)
                        ->withInput();
                });
        });

        

    }
}
