<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class KioskController extends Controller
{
    /**
     * Display the kiosk interface
     */
    public function index()
    {
        return view('kiosk');
    }

    /**
     * Handle dine-in selection
     */
    public function dineIn(Request $request)
    {
        // Store the selection in session
        session(['order_type' => 'dine_in']);
        
        // Redirect back with message for now
        return redirect()->back()->with('message', 'Dine-in selected! Order type saved.');
    }

    /**
     * Handle take-out selection
     */
    public function takeOut(Request $request)
    {
        // Store the selection in session
        session(['order_type' => 'take_out']);
        
        // Redirect back with message for now
        return redirect()->back()->with('message', 'Take-out selected! Order type saved.');
    }

    /**
     * Display the main menu page
     */
    public function main(Request $request)
    {
        $orderType = $request->input('order_type'); // 'dine-in' or 'take-out'
        
        // Store the order type in session
        session(['order_type' => $orderType]);
        
        return view('kioskMain');
    }
}