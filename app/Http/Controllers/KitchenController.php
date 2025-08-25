<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Ingredient;
use App\Models\MenuItemIngredient;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class KitchenController extends Controller
{
    public function index()
    {
        $pendingOrders = Order::with('orderItems.menuItem')
            ->where('status', 'pending')
            ->orderBy('created_at', 'asc')
            ->get();

        $processingOrders = Order::with('orderItems.menuItem')
            ->where('status', 'processing')
            ->orderBy('started_at', 'asc')
            ->get();

        $completedOrders = Order::with('orderItems.menuItem')
            ->where('status', 'completed')
            ->where('completed_at', '>=', now()->subHours(2))
            ->orderBy('completed_at', 'desc')
            ->limit(10)
            ->get();

        return view('kitchen', compact('pendingOrders', 'processingOrders', 'completedOrders'));
    }

    // Add the missing start method
    public function start($orderId)
    {
        return $this->startOrder($orderId);
    }

    public function startOrder($orderId)
    {
        $order = Order::findOrFail($orderId);
        
        $order->update([
            'status' => 'processing',
            'started_at' => now()
        ]);

        return redirect()->back()->with('success', 'Order started successfully!');
    }

    public function completeOrder($orderId)
    {
        $order = Order::findOrFail($orderId);
        
        $order->update([
            'status' => 'completed',
            'completed_at' => now()
        ]);
        
        return redirect()->back()->with('success', 'Order completed!');
    }

    // Remove the duplicate ingredient deduction since it's now handled in KioskController
    // Ingredients are already deducted when the order is placed, not when completed
}