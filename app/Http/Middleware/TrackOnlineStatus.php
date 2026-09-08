<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class TrackOnlineStatus
{
    public function handle(Request $request, Closure $next): Response
    {
        /*
        |--------------------------------------------------------------------------
        | Web Guard
        | Users + Admins
        |--------------------------------------------------------------------------
        */

        if (Auth::guard('web')->check()) {
            $user = Auth::guard('web')->user();

            if (method_exists($user, 'markOnline')) {
                $user->markOnline();
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Therapist Guard
        |--------------------------------------------------------------------------
        */

        if (Auth::guard('therapist')->check()) {
            $therapist = Auth::guard('therapist')->user();

            if (method_exists($therapist, 'markOnline')) {
                $therapist->markOnline();
            }
        }

        return $next($request);
    }
}