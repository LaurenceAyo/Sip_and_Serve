<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class AuthController extends Controller
{
    // Store your PIN hash here or in config/database
    private $correctPinHash = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'; // "password" hashed
    
    public function showPinLogin()
    {
        // If already authenticated, redirect to intended page
        if (Session::has('pin_authenticated')) {
            return redirect()->intended('/admin');
        }
        
        return view('auth.pin-login');
    }
    
    public function pinLogin(Request $request)
    {
        $request->validate([
            'pin' => 'required|digits:4'
        ]);
        
        // Check PIN against stored hash
        if (Hash::check($request->pin, $this->correctPinHash)) {
            // Set authentication session
            Session::put('pin_authenticated', true);
            Session::put('pin_authenticated_at', now());
            
            // Redirect to intended page or default
            return redirect()->intended('/admin')->with('success', 'Access granted');
        }
        
        // Failed authentication
        return back()->withErrors([
            'pin' => 'Invalid PIN. Please try again.'
        ])->withInput();
    }
    
    public function pinLogout()
    {
        Session::forget(['pin_authenticated', 'pin_authenticated_at']);
        return redirect()->route('pin.login')->with('message', 'Logged out successfully');
    }
}