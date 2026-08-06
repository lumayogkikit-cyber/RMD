<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ForceHsts
{
    /**
     * Handle an incoming request and add HSTS header for HTTPS responses.
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // Only add HSTS when request is secure
        if ($request->isSecure()) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains; preload');
        }

        return $response;
    }
}
