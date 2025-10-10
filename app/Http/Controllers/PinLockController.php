<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class PinLockController extends Controller
{
    public function showLock()
    {
        // Must be logged in to see PIN lock
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        // If already verified, redirect based on role
        if (session('pin_verified') === true) {
            return $this->redirectBasedOnRole();
        }

        return view('auth.pin-lock');
    }

    public function verify(Request $request)
    {
        Log::info('PIN Verification Attempt', [
            'input_pin' => $request->pin,
            'user' => Auth::user()->name ?? 'Unknown'
        ]);

        $request->validate([
            'pin' => 'required|string|size:4'
        ]);

        $correctPin = env('SYSTEM_PIN', '1234');

        if ($request->pin === $correctPin) {
            // Unlock and reset activity timer
            session([
                'pin_verified' => true,
                'last_activity' => time()
            ]);
            
            Log::info('PIN Verified - System Unlocked');

            $redirectUrl = $this->getRedirectUrl();

            return response()->json([
                'success' => true,
                'redirect' => $redirectUrl
            ]);
        }

        Log::warning('Invalid PIN Attempt');

        return response()->json([
            'success' => false,
            'message' => 'Incorrect PIN. Please try again.'
        ], 401);
    }

    /**
     * Get redirect URL based on user role
     */
    private function getRedirectUrl()
    {
        $user = Auth::user();
        
        return match($user->role) {
            'admin', 'manager' => route('dashboard'),
            'cashier' => route('cashier.index'),
            'kitchen' => route('kitchen.index'),
            default => route('dashboard')
        };
    }

    /**
     * Redirect based on user role
     */
    private function redirectBasedOnRole()
    {
        return redirect($this->getRedirectUrl());
    }
}