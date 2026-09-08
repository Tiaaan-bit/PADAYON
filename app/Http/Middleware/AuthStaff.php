<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AuthStaff
{
    public function handle(Request $request, Closure $next): Response
    {
        // Must be logged in through the web guard
        if (!Auth::guard('web')->check()) {
            return redirect()
                ->route('login')
                ->with('error', 'Please log in to continue.');
        }

        $user = Auth::guard('web')->user();

        // Only staff can access staff routes
        if (!$user->isStaff()) {
            abort(403, 'Unauthorized access.');
        }

        return $next($request);
    }
}
