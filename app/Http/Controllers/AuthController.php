<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    /**
     * Show PIN login form
     */
    public function showPinLogin(Request $request)
    {
        $pinType = $request->get('type', 'general');
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login');
        }

        return view('auth.pin-login', [
            'pinType' => $pinType,
            'user' => $user,
            'title' => $this->getPinTitle($pinType)
        ]);
    }

    /**
     * Handle PIN authentication
     */
    public function pinLogin(Request $request)
    {
        $request->validate([
            'pin' => 'required|string|size:4',
            'type' => 'sometimes|string'
        ]);

        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        $pinType = $request->get('type', 'general');
        $enteredPin = $request->pin;

        // Check if PIN exists for this user and type
        $sessionPinKey = "pin_hash_{$pinType}_{$user->id}";
        $storedPinHash = Session::get($sessionPinKey);

        // If no PIN is set, create one with the entered PIN
        if (!$storedPinHash) {
            $pinHash = Hash::make($enteredPin);
            Session::put($sessionPinKey, $pinHash);
            $this->authenticatePin($pinType);
            
            Log::info("PIN created for user {$user->id}, type: {$pinType}");
            
            return redirect()->intended($this->getDefaultRoute($user->role))
                ->with('success', 'PIN created and authenticated successfully!');
        }

        // Verify the PIN
        if (Hash::check($enteredPin, $storedPinHash)) {
            $this->authenticatePin($pinType);
            
            Log::info("PIN authenticated for user {$user->id}, type: {$pinType}");
            
            return redirect()->intended($this->getDefaultRoute($user->role))
                ->with('success', 'PIN authenticated successfully!');
        }

        Log::warning("Failed PIN attempt for user {$user->id}, type: {$pinType}");
        
        return back()->withErrors(['pin' => 'Invalid PIN. Please try again.'])
            ->withInput($request->except('pin'));
    }

    /**
     * Handle PIN logout
     */
    public function pinLogout(Request $request)
    {
        $pinType = $request->get('type', 'general');
        $sessionKey = "pin_authenticated_{$pinType}";
        $sessionTimeKey = "pin_authenticated_at_{$pinType}";

        Session::forget([$sessionKey, $sessionTimeKey]);

        Log::info("PIN logout for user " . Auth::id() . ", type: {$pinType}");

        return redirect()->route('pin.login', ['type' => $pinType])
            ->with('message', 'PIN session ended. Please re-enter your PIN.');
    }

    /**
     * Reset PIN for current user
     */
    public function resetPin(Request $request)
    {
        $request->validate([
            'current_pin' => 'required|string|size:4',
            'new_pin' => 'required|string|size:4',
            'confirm_pin' => 'required|string|size:4|same:new_pin',
            'type' => 'sometimes|string'
        ]);

        $user = Auth::user();
        $pinType = $request->get('type', 'general');
        $sessionPinKey = "pin_hash_{$pinType}_{$user->id}";
        $storedPinHash = Session::get($sessionPinKey);

        // Verify current PIN
        if (!$storedPinHash || !Hash::check($request->current_pin, $storedPinHash)) {
            return back()->withErrors(['current_pin' => 'Current PIN is incorrect.']);
        }

        // Set new PIN
        $newPinHash = Hash::make($request->new_pin);
        Session::put($sessionPinKey, $newPinHash);

        Log::info("PIN reset for user {$user->id}, type: {$pinType}");

        return back()->with('success', 'PIN updated successfully!');
    }

    /**
     * Authenticate PIN for session
     */
    private function authenticatePin(string $pinType): void
    {
        $sessionKey = "pin_authenticated_{$pinType}";
        $sessionTimeKey = "pin_authenticated_at_{$pinType}";

        Session::put($sessionKey, true);
        Session::put($sessionTimeKey, now());
    }

    /**
     * Get PIN title for display
     */
    private function getPinTitle(string $pinType): string
    {
        return match($pinType) {
            'pos' => 'POS System Access',
            'admin' => 'Admin Panel Access',
            'cashier' => 'Cashier System Access',
            'kitchen' => 'Kitchen System Access',
            default => 'System Access'
        };
    }

    /**
     * Get default route based on user role
     */
    private function getDefaultRoute(string $role): string
    {
        return match($role) {
            'admin' => '/admin/users',
            'manager' => '/dashboard',
            'cashier' => '/cashier',
            'kitchen' => '/kitchen',
            default => '/dashboard'
        };
    }
}