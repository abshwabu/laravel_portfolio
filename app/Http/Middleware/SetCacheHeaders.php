<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetCacheHeaders
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // For safe HTML page requests (GET / HEAD), instruct browsers and proxies
        // to revalidate so HTML changes and updated asset hashes are seen immediately.
        if ($request->isMethodSafe() && ! $request->expectsJson()) {
            $contentType = $response->headers->get('Content-Type', '');
            if (str_contains($contentType, 'text/html') || $contentType === '') {
                $response->headers->set('Cache-Control', 'public, max-age=0, must-revalidate');
            }
        }

        return $response;
    }
}
