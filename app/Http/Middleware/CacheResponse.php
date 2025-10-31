<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class CacheResponse
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, int $ttl = 300): Response
    {
        // Skip caching untuk request non-GET atau dengan auth changes
        if (!$request->isMethod('GET') || $request->user()) {
            return $next($request);
        }

        // Generate unique cache key berdasarkan URL + query params
        $key = 'response_' . md5($request->fullUrl());

        // Coba ambil dari cache
        $cachedResponse = Cache::get($key);
        
        if ($cachedResponse) {
            return response($cachedResponse['content'], $cachedResponse['status'])
                ->withHeaders($cachedResponse['headers'])
                ->header('X-Cache', 'HIT');
        }

        // Process request
        $response = $next($request);

        // Cache hanya response sukses (200)
        if ($response->isSuccessful() && $response->getStatusCode() === 200) {
            Cache::put($key, [
                'content' => $response->getContent(),
                'status' => $response->getStatusCode(),
                'headers' => $response->headers->all(),
            ], $ttl);

            $response->header('X-Cache', 'MISS');
        }

        return $response;
    }
}
