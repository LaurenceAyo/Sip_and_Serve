<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class KitchenController extends Controller
{
    public function index()
    {
        // Get pending orders (paid but not started)
        $pendingOrders = Order::with('orderItems.menuItem')
            ->where('payment_status', 'paid')
            ->where('status', 'pending')
            ->latest()
            ->get();

        // Get orders currently being prepared
        $processingOrders = Order::with('orderItems.menuItem')
            ->where('status', 'preparing')
            ->latest()
            ->get();

        // Get recently completed orders (last 2 hours)
        $completedOrders = Order::with('orderItems.menuItem')
            ->where('status', 'completed')
            ->where('completed_at', '>=', now()->subHours(2))
            ->latest('completed_at')
            ->take(10)
            ->get();

        return view('kitchen', compact('pendingOrders', 'processingOrders', 'completedOrders'));
    }

    // Start preparing an order
    public function start(Order $order)
    {
        // Calculate estimated prep time if not set
        if (!$order->estimated_prep_time) {
            $order->calculateEstimatedPrepTime();
        }

        $order->update([
            'status' => 'preparing',
            'started_at' => now()
        ]);

        return redirect()->back()->with('success', 'Order started! Estimated completion: ' . 
            $order->estimated_completion_time->format('g:i A'));
    }

    // Complete an order
    public function completeOrder(Order $order)
    {
        $order->update([
            'status' => 'completed',
            'completed_at' => now()
        ]);

        $actualPrepTime = $order->total_prep_time;
        $estimatedTime = $order->estimated_prep_time;
        
        $message = "Order completed! ";
        if ($actualPrepTime && $estimatedTime) {
            $variance = $actualPrepTime - $estimatedTime;
            if ($variance > 5) {
                $message .= "Took {$variance} minutes longer than estimated.";
            } elseif ($variance < -5) {
                $message .= "Completed {$variance} minutes faster than estimated!";
            } else {
                $message .= "Completed on time!";
            }
        }

        return redirect()->back()->with('success', $message);
    }

    // Get real-time kitchen data for AJAX updates
    public function getData()
    {
        $pendingOrders = Order::with('orderItems.menuItem')
            ->where('payment_status', 'paid')
            ->where('status', 'pending')
            ->latest()
            ->get();

        $processingOrders = Order::with('orderItems.menuItem')
            ->where('status', 'preparing')
            ->latest()
            ->get();

        $completedOrders = Order::with('orderItems.menuItem')
            ->where('status', 'completed')
            ->where('completed_at', '>=', now()->subHours(2))
            ->latest('completed_at')
            ->take(10)
            ->get();

        return response()->json([
            'pending' => $pendingOrders,
            'processing' => $processingOrders,
            'completed' => $completedOrders
        ]);
    }

    // Legacy method - keep for compatibility
    public function receiveOrder(Order $order)
    {
        return $this->start($order);
    }
}