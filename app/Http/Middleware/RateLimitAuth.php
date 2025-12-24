<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Symfony\Component\HttpFoundation\Response;

class RateLimitAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $key = 'auth:' . $request->ip();

        // Limit to 5 attempts per 15 minutes
        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => "Terlalu banyak percobaan login. Silakan tunggu {$seconds} detik lagi.",
                    'retry_after' => $seconds
                ], 429);
            }
            
            return back()->withErrors([
                'email' => "Terlalu banyak percobaan login. Silakan tunggu {$seconds} detik lagi.",
            ])->withInput($request->only('email'));
        }

        RateLimiter::hit($key, 900); // 15 minutes

        return $next($request);
    }
}

