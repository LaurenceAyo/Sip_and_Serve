<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{


    public function showLoginForm(Request $request)
    {
        // Store flag if they came from Staff Login button
        if ($request->query('from') === 'staff') {
            session(['from_staff_login' => true]);
        }

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

            // Set PIN as verified on fresh login (no PIN needed on login)
            session([
                'pin_verified' => true,
                'last_activity' => time()
            ]);

            // CUSTOMER REDIRECT - Block customers from staff areas
            // ⭐ CUSTOMER REDIRECT - Block customers from staff areas
            if ($user->role === 'customer') {
                Log::info('Customer logged in, redirecting to kiosk: ' . $user->email);

                // Check if they clicked "Staff Login" button from cover page
                $fromStaffButton = $request->session()->get('from_staff_login', false);

                if ($fromStaffButton) {
                    // Clear the flag
                    $request->session()->forget('from_staff_login');
                    // Show popup - they tried to use staff login
                    return redirect()->route('kiosk.index')->with('restricted_access', true);
                }

                // Normal kiosk login - no popup
                return redirect()->route('kiosk.index');
            }

            // Role-based redirection for STAFF
            return match ($user->role) {
                'admin' => redirect()->intended(route('dashboard')),
                'manager' => redirect()->intended(route('dashboard')),
                'cashier' => redirect()->intended(route('cashier.index')),
                'kitchen' => redirect()->intended(route('kitchen.index')),
                default => redirect()->intended(route('kiosk.index'))
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
        // Clear PIN verification on logout
        session()->forget('pin_verified');
        session()->forget('last_activity');

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
