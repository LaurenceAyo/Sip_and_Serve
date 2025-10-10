<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class BlockCustomerAccess
{
    /**
     * Block customers from accessing staff-only routes
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check() && Auth::user()->role === 'customer') {
            // Block access to any non-kiosk routes
            if (!$this->isKioskRoute($request)) {
                return redirect()->route('kiosk.index')
                    ->with('restricted_access', true); // Add this flag
            }
        }

        return $next($request);
    }

    /**
     * Check if the current route is a kiosk route
     */
    private function isKioskRoute(Request $request): bool
    {
        $path = $request->path();
        $routeName = $request->route() ? $request->route()->getName() : '';

        // Allow kiosk routes
        $allowedPaths = [
            'kiosk',
            'qr/payment',
            'category',
        ];

        $allowedRouteNames = [
            'kiosk.index',
            'kiosk.main',
            'kiosk.dineIn',
            'kiosk.takeOut',
            'kiosk.placeOrder',
            'kiosk.addToCart',
            'kiosk.removeFromCart',
            'kiosk.getCart',
            'kiosk.updateCartItem',
            'kiosk.removeCartItem',
            'kiosk.reviewOrder',
            'kiosk.checkout',
            'kiosk.cancelOrder',
            'kiosk.processOrder',
            'kiosk.submitOrder',
            'kiosk.orderConfirmation',
            'kiosk.orderConfirmationSuccess',
            'kiosk.payment',
            'kiosk.processPayment',
            'kiosk.processCashPayment',
            'kiosk.processMayaPayment',
            'kiosk.processGCashPayment',
            'kiosk.paymentSuccess',
            'kiosk.paymentFailed',
            'kiosk.payment.success',
            'kiosk.payment.failed',
            'kiosk.updateOrderType',
            'qr.payment.show',
            'qr.payment.generate',
            'qr.payment.status',
            'qr.payment.process',
            'logout',
            'profile.edit',
            'profile.update',
            'getCategoryItems',
            'cover',
        ];

        // Check if path starts with allowed paths
        foreach ($allowedPaths as $allowedPath) {
            if (str_starts_with($path, $allowedPath)) {
                return true;
            }
        }

        // Check if route name is in allowed list
        if (in_array($routeName, $allowedRouteNames)) {
            return true;
        }

        return false;
    }
}