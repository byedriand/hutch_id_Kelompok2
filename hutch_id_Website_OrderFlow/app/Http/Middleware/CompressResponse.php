<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CompressResponse
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // Check if client accepts gzip encoding
        if (strpos($request->header('Accept-Encoding'), 'gzip') !== false) {
            $response->header('Content-Encoding', 'gzip');
        }

        // Add cache headers for static assets
        if ($request->is('build/*')) {
            $response->header('Cache-Control', 'public, max-age=31536000, immutable');
        }

        return $response;
    }
}
