<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function kitchen()
    {
        $pendingOrders = Order::with(['orderItems.menuItem'])
            ->where('status', 'pending')
            ->orderBy('created_at')
            ->get();

        $processingOrders = Order::with(['orderItems.menuItem'])
            ->where('status', 'processing')
            ->orderBy('updated_at')
            ->get();

        return view('kitchen', compact('pendingOrders', 'processingOrders'));
    }

    public function startOrder($id)
    {
        $order = Order::findOrFail($id);
        $order->update([
            'status' => 'processing',
            'started_at' => now()
        ]);

        return redirect()->route('kitchen.index')->with('success', 'Order started!');
    }

    public function completeOrder($id)
    {
        $order = Order::findOrFail($id);
        $order->update([
            'status' => 'completed',
            'completed_at' => now()
        ]);

        return redirect()->route('kitchen.index')->with('success', 'Order completed!');
    }
}