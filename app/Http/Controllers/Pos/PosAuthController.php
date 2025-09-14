<?php

namespace App\Http\Controllers\Pos;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;

class PosAuthController extends Controller
{
    public function showPinSetup()
    {
        // Ensure user is logged in but PIN not set
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        // If PIN already exists in session, redirect to POS
        if (Session::has('pos_pin_hash')) {
            return redirect()->route('pos.pin.verify');
        }

        return view('pos.auth.pin-setup');
    }

    public function setupPin(Request $request)
    {
        $request->validate([
            'pin' => 'required|digits:4|confirmed'
        ]);

        // Store PIN hash in session (or database if you prefer)
        Session::put('pos_pin_hash', Hash::make($request->pin));
        Session::put('pos_pin_setup_at', now());

        return redirect()->route('pos.pin.verify')->with('success', 'PIN setup complete');
    }

    public function showPinVerify()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        if (!Session::has('pos_pin_hash')) {
            return redirect()->route('pos.pin.setup');
        }

        // If already verified, redirect to POS dashboard
        if (Session::has('pos_authenticated')) {
            return redirect()->route('pos.dashboard');
        }

        return view('pos.auth.pin-verify');
    }

    public function verifyPin(Request $request)
    {
        $request->validate([
            'pin' => 'required|digits:4'
        ]);

        $storedHash = Session::get('pos_pin_hash');
        
        if (Hash::check($request->pin, $storedHash)) {
            Session::put('pos_authenticated', true);
            Session::put('pos_authenticated_at', now());
            
            return redirect()->route('pos.dashboard')->with('success', 'Welcome to POS');
        }

        return back()->withErrors(['pin' => 'Invalid PIN']);
    }

    public function lockPos()
    {
        Session::forget('pos_authenticated');
        return redirect()->route('pos.pin.verify');
    }

    public function resetPin()
    {
        Session::forget(['pos_pin_hash', 'pos_authenticated']);
        return redirect()->route('pos.pin.setup');
    }
}