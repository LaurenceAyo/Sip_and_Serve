<?php

namespace App\Http\Controllers\Pos;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class PosAuthController extends Controller
{
    /**
     * Show PIN setup form
     */
    public function showPinSetup()
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        // Check if user can access POS
        if (!$user->canAccess('cashier') && !$user->isAdmin()) {
            return redirect()->route('dashboard')
                ->with('error', 'You do not have permission to access the POS system.');
        }

        return view('pos.pin-setup', ['user' => $user]);
    }

    /**
     * Setup PIN for POS access
     */
    public function setupPin(Request $request)
    {
        $request->validate([
            'pin' => 'required|string|size:4|regex:/^[0-9]{4}$/',
            'confirm_pin' => 'required|string|same:pin'
        ]);

        $user = Auth::user();
        $pinHash = Hash::make($request->pin);
        
        Session::put("pos_pin_hash_{$user->id}", $pinHash);
        Session::put('pos_authenticated', true);
        Session::put('pos_authenticated_at', now());

        Log::info("POS PIN setup completed for user {$user->id}");

        return redirect()->route('cashier.index')
            ->with('success', 'POS PIN setup completed successfully!');
    }

    /**
     * Show PIN verification form
     */
    public function showPinVerify()
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        return view('pos.pin-verify', ['user' => $user]);
    }

    /**
     * Verify PIN for POS access
     */
    public function verifyPin(Request $request)
    {
        $request->validate([
            'pin' => 'required|string|size:4'
        ]);

        $user = Auth::user();
        $storedPinHash = Session::get("pos_pin_hash_{$user->id}");

        if (!$storedPinHash) {
            return redirect()->route('pos.pin.setup');
        }

        if (Hash::check($request->pin, $storedPinHash)) {
            Session::put('pos_authenticated', true);
            Session::put('pos_authenticated_at', now());

            Log::info("POS PIN verified for user {$user->id}");

            return redirect()->route('cashier.index')
                ->with('success', 'POS access granted!');
        }

        Log::warning("Failed POS PIN attempt for user {$user->id}");

        return back()->withErrors(['pin' => 'Invalid PIN. Please try again.']);
    }

    /**
     * Lock POS system
     */
    public function lockPos()
    {
        Session::forget(['pos_authenticated', 'pos_authenticated_at']);
        
        Log::info("POS locked by user " . Auth::id());

        return redirect()->route('pos.pin.verify')
            ->with('message', 'POS system locked. Please enter your PIN to continue.');
    }

    /**
     * Reset POS PIN
     */
    public function resetPin(Request $request)
    {
        $request->validate([
            'current_pin' => 'required|string|size:4',
            'new_pin' => 'required|string|size:4|regex:/^[0-9]{4}$/',
            'confirm_pin' => 'required|string|same:new_pin'
        ]);

        $user = Auth::user();
        $storedPinHash = Session::get("pos_pin_hash_{$user->id}");

        if (!$storedPinHash || !Hash::check($request->current_pin, $storedPinHash)) {
            return back()->withErrors(['current_pin' => 'Current PIN is incorrect.']);
        }

        $newPinHash = Hash::make($request->new_pin);
        Session::put("pos_pin_hash_{$user->id}", $newPinHash);

        Log::info("POS PIN reset for user {$user->id}");

        return back()->with('success', 'POS PIN updated successfully!');
    }
}