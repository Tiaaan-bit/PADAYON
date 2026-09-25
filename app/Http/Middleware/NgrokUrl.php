<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Symfony\Component\HttpFoundation\Response;

class NgrokUrl
{
    public function handle(Request $request, Closure $next): Response
    {
        $forwardedHost = $request->header('x-forwarded-host');
        $forwardedProto = $request->header('x-forwarded-proto');

        if ($forwardedHost && $forwardedProto) {
            URL::forceRootUrl(
                $forwardedProto . '://' . $forwardedHost
            );

            URL::forceScheme($forwardedProto);
        }

        return $next($request);
    }
}