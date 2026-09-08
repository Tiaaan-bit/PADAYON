<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AuthUser
{
    public function handle(Request $request, Closure $next): Response
    {
        // Therapist cannot access user pages
        if (Auth::guard('therapist')->check()) {
            abort(403, 'Unauthorized access.');
        }

        // Must be logged in through web guard
        if (!Auth::guard('web')->check()) {
            return redirect()
                ->route('login')
                ->with('error', 'Please log in to continue.');
        }

        /** @var User $user */
        $user = Auth::guard('web')->user();

        // Admin cannot access user pages
        if ($user->isAdmin()) {
            abort(403, 'Unauthorized access.');
        }

        // Staff cannot access user pages
        if ($user->isStaff()) {
            abort(403, 'Unauthorized access.');
        }

        // Must have verified email
        if (!$user->hasVerifiedEmail()) {
            return redirect()
                ->route('verification.notice')
                ->with('info', 'Please verify your email address first.');
        }

        return $next($request);
    }
}
