<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  $role
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please log in to access this area.');
        }
        $user = Auth::user();

        // Check if user account is active
        if (!$user->isActive()) {
            Auth::logout();
            return redirect()->route('login')->with('error', 'Your account has been deactivated.');
        }

        // Admin can access everything
        if ($user->isAdmin()) {
            return $next($request);
        }

        // Check specific role permissions
        switch ($role) {
            case 'admin':
                if (!$user->isAdmin()) {
                    return $this->unauthorized($request, 'Administrator access required.');
                }
                break;

            case 'manager':
                if (!($user->isManager() || $user->isAdmin())) {
                    return $this->unauthorized($request, 'Manager access required.');
                }
                break;

            case 'cashier':
                if (!($user->isCashier() || $user->isManager() || $user->isAdmin())) {
                    return $this->unauthorized($request, 'Cashier access required.');
                }
                break;

            case 'kitchen':
                if (!($user->isKitchen() || $user->isManager() || $user->isAdmin())) {
                    return $this->unauthorized($request, 'Kitchen staff access required.');
                }
                break;

            case 'staff':
                // Any authenticated staff member
                if (!in_array($user->role, ['admin', 'manager', 'cashier', 'kitchen'])) {
                    return $this->unauthorized($request, 'Staff access required.');
                }
                break;

            default:
                return $this->unauthorized($request, 'Access denied.');
        }

        // Update last login time
        $user->updateLastLogin();

        return $next($request);
    }

    /**
     * Handle unauthorized access
     */
    private function unauthorized(Request $request, string $message): Response
    {
        if ($request->expectsJson()) {
            return response()->json([
                'error' => $message,
                'code' => 403
            ], 403);
        }

        // Redirect to appropriate dashboard based on user role
        $user = Auth::user();
        $redirectRoute = $this->getDefaultRouteForRole($user->role);

        return redirect()->route($redirectRoute)
            ->with('error', $message);
    }

    /**
     * Get default route for user role
     */
    private function getDefaultRouteForRole(string $role): string
    {
        return match ($role) {
            'admin' => 'dashboard',
            'manager' => 'dashboard',
            'cashier' => 'cashier.index',
            'kitchen' => 'kitchen.index',
            default => 'dashboard'
        };
    }
}
