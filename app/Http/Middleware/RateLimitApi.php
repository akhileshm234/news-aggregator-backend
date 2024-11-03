<?php

namespace App\Http\Middleware;

use Illuminate\Cache\RateLimiter;
use Symfony\Component\HttpFoundation\Response;

class RateLimitApi
{
    public function handle($request, \Closure $next, $maxAttempts = 60)
    {
        $key = optional($request->user())->id ?: $request->ip();
        
        if (RateLimiter::tooManyAttempts($key, $maxAttempts)) {
            return response()->json([
                'message' => 'Too many requests',
            ], Response::HTTP_TOO_MANY_REQUESTS);
        }

        RateLimiter::hit($key);
        return $next($request);
    }
} 