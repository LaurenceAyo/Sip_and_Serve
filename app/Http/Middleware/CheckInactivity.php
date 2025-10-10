<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CheckInactivity
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        // Skip for PIN-related routes
        if ($request->routeIs(['pin.lock', 'pin.verify'])) {
            return $next($request);
        }

        // Inactivity timeout in seconds (15 minutes = 900 seconds)
        $inactivityTimeout = 900; // 15 minutes

        $lastActivity = session('last_activity', time());
        $currentTime = time();
        $inactiveTime = $currentTime - $lastActivity;

        Log::info('Inactivity Check', [
            'last_activity' => $lastActivity,
            'current_time' => $currentTime,
            'inactive_for' => $inactiveTime . ' seconds',
            'timeout' => $inactivityTimeout,
            'will_lock' => $inactiveTime > $inactivityTimeout
        ]);

        // If inactive for more than 15 minutes, lock the system
        if ($inactiveTime > $inactivityTimeout) {
            Log::warning('System locked due to inactivity', [
                'inactive_minutes' => round($inactiveTime / 60, 2),
                'user' => optional(Auth::user())->email ?? 'unknown'
            ]);

            // Lock the system
            session(['pin_verified' => false]);

            // Redirect to PIN lock
            if ($request->expectsJson()) {
                return response()->json([
                    'error' => 'Session locked due to inactivity',
                    'redirect' => route('pin.lock')
                ], 403);
            }

            return redirect()->route('pin.lock')
                ->with('message', 'Session locked due to inactivity. Please enter your PIN.');
        }

        // Update last activity time
        session(['last_activity' => $currentTime]);

        return $next($request);
    }
}