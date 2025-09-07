<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\Ingredient;
use App\Models\Inventory;

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

        // Get ingredients from your existing table
        $inventory = Inventory::with('ingredient')
            ->join('ingredients', 'inventory.menu_item_id', '=', 'ingredients.id')
            ->orderBy('ingredients.name')
            ->select('inventory.*')
            ->get();

        // Pass both inventory and ingredients to the view
        return view('dashboard', compact('inventory'));
    }
}
