<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;

class PinAuthentication
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, string $pinType = 'general')
    {
        // Check if user is logged in first
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();
        $sessionKey = "pin_authenticated_{$pinType}";
        $sessionTimeKey = "pin_authenticated_at_{$pinType}";

        // Check if PIN is authenticated for this type
        if (!Session::has($sessionKey)) {
            return redirect()->route('pin.login', ['type' => $pinType])
                ->with('pin_required', true);
        }

        // Check if PIN session has expired
        $authenticatedAt = Session::get($sessionTimeKey);
        $expiryHours = $this->getExpiryHours($pinType);
        
        if ($authenticatedAt && now()->diffInHours($authenticatedAt) > $expiryHours) {
            Session::forget([$sessionKey, $sessionTimeKey]);
            return redirect()->route('pin.login', ['type' => $pinType])
                ->with('message', 'PIN session expired for security. Please re-enter your PIN.');
        }

        // Update last activity time
        Session::put($sessionTimeKey, now());

        return $next($request);
    }

    /**
     * Get expiry hours based on PIN type and user role
     */
    private function getExpiryHours(string $pinType): int
    {
        return match($pinType) {
            'pos' => 4,      // POS operations expire after 4 hours
            'admin' => 2,    // Admin operations expire after 2 hours
            'cashier' => 8,  // Cashier operations expire after 8 hours
            'kitchen' => 12, // Kitchen operations expire after 12 hours
            default => 6     // General operations expire after 6 hours
        };
    }
}