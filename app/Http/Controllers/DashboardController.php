<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\Ingredient;

class DashboardController extends Controller
{
    public function index()
    {

        // Get ingredients for the dashboard
        $ingredients = Ingredient::all();
        // DEBUG: Log authentication status
        Log::emergency('=== DASHBOARD ACCESS ATTEMPT ===');
        Log::emergency('Auth::check(): ' . (Auth::check() ? 'TRUE' : 'FALSE'));
        Log::emergency('Auth::user(): ' . (Auth::user() ? Auth::user()->email : 'NULL'));
        Log::emergency('Session ID: ' . session()->getId());
        Log::emergency('================================');

        // Get ingredients from your existing table
        $ingredients = Ingredient::all();
        // For now, don't check auth to see what happens
        return view('dashboard', compact('ingredients'));
    }
}