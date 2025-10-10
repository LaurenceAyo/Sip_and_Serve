<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPinLock
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Skip PIN check for PIN-related routes
        if ($request->routeIs(['pin.lock', 'pin.verify'])) {
            return $next($request);
        }

        // If PIN not verified, redirect to PIN lock
        if (session('pin_verified') !== true) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'PIN verification required'], 403);
            }
            return redirect()->route('pin.lock');
        }

        return $next($request);
    }
}