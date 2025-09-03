<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    public function index()
    {
        // DEBUG: Log authentication status
        Log::emergency('=== DASHBOARD ACCESS ATTEMPT ===');
        Log::emergency('Auth::check(): ' . (Auth::check() ? 'TRUE' : 'FALSE'));
        Log::emergency('Auth::user(): ' . (Auth::user() ? Auth::user()->email : 'NULL'));
        Log::emergency('Session ID: ' . session()->getId());
        Log::emergency('================================');

        // For now, don't check auth to see what happens
        return view('dashboard');
    }
}