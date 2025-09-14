<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;

class PosSecurityMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // Check if user is logged in
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        // Check if PIN is set up
        if (!Session::has('pos_pin_hash')) {
            return redirect()->route('pos.pin.setup');
        }

        // Check if POS is authenticated
        if (!Session::has('pos_authenticated')) {
            return redirect()->route('pos.pin.verify');
        }

        // Check if session expired (e.g., after 2 hours of inactivity)
        $lastActivity = Session::get('pos_authenticated_at');
        if ($lastActivity && now()->diffInHours($lastActivity) > 2) {
            Session::forget('pos_authenticated');
            return redirect()->route('pos.pin.verify')->with('message', 'Session expired for security');
        }

        // Update last activity
        Session::put('pos_authenticated_at', now());

        return $next($request);
    }
}