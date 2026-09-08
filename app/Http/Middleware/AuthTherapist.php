<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AuthTherapist
{
    public function handle(Request $request, Closure $next): Response
    {
        // Therapist is logged in
        if (Auth::guard('therapist')->check()) {
            return $next($request);
        }

        // Admin, staff, or user is logged in
        if (Auth::guard('web')->check()) {
            abort(403, 'Unauthorized access.');
        }

        // Nobody is logged in
        return redirect()
            ->route('login')
            ->with('error', 'Please log in to continue.');
    }
}
