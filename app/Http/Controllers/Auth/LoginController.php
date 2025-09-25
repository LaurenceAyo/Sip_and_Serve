<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    /**
     * Show the login form
     */
    public function showLoginForm()
    {
        return view('welcome');
    }

    /**
     * Handle login request
     */
    public function login(Request $request)
    {
        // Validate input
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // DEBUG: Check what's in the database
        $user = \App\Models\User::where('email', $credentials['email'])->first();

        Log::info('DEBUG Login Details:', [
            'email' => $credentials['email'],
            'user_exists' => $user ? 'yes' : 'no',
            'password_length' => strlen($credentials['password']),
            'user_password_hash' => $user ? substr($user->password, 0, 20) . '...' : 'no_user',
            'manual_check' => $user ? Hash::check($credentials['password'], $user->password) : 'no_user'
        ]);

        // Attempt authentication
        if (Auth::attempt($credentials, $request->filled('remember'))) {
            // Authentication successful
            $request->session()->regenerate();

            Log::info('Login successful for: ' . $credentials['email']);

            $user = Auth::user();

            // Update last login
            $user->updateLastLogin();

            // Role-based redirection
            return match($user->role) {
                'admin' => redirect()->intended(route('dashboard')),
                'manager' => redirect()->intended(route('dashboard')), 
                'cashier' => redirect()->intended(route('cashier.index')),
                'kitchen' => redirect()->intended(route('kitchen.index')),
                default => redirect()->intended(route('dashboard'))
            };
        }

        // Authentication failed
        Log::warning('Login failed for: ' . $credentials['email']);

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    /**
     * Handle logout
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}