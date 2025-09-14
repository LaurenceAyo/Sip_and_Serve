<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class PinAuthentication
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        // Check if PIN is authenticated
        if (!Session::has('pin_authenticated')) {
            return redirect()->route('pin.login');
        }

        // Optional: Check if PIN session has expired (e.g., after 8 hours)
        $authenticatedAt = Session::get('pin_authenticated_at');
        if ($authenticatedAt && now()->diffInHours($authenticatedAt) > 8) {
            Session::forget(['pin_authenticated', 'pin_authenticated_at']);
            return redirect()->route('pin.login')->with('message', 'Session expired. Please enter PIN again.');
        }

        return $next($request);
    }
}