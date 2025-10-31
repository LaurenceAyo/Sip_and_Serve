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
        // Show ALL pending orders (both paid and unpaid from kiosk)
        // Kitchen needs to see them all to start preparing
        $pendingOrders = Order::with('orderItems.menuItem')
            ->where('status', 'pending')
            ->orderBy('created_at', 'asc')
            ->get();

        Log::info('Kitchen - Pending orders loaded', [
            'count' => $pendingOrders->count(),
            'order_ids' => $pendingOrders->pluck('id')->toArray(),
            'payment_statuses' => $pendingOrders->pluck('payment_status', 'id')->toArray()
        ]);

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

        $cancelledOrders = Order::with('orderItems.menuItem')
            ->where('status', 'cancelled')
            ->where('updated_at', '>=', now()->subHours(2))
            ->orderBy('updated_at', 'desc')
            ->get();

        return view('kitchen', compact('pendingOrders', 'processingOrders', 'completedOrders', 'cancelledOrders'));
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

        Log::info('Kitchen - Order started', [
            'order_id' => $order->id,
            'status' => 'processing'
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

        Log::info('Kitchen - Order completed', [
            'order_id' => $order->id,
            'status' => 'completed'
        ]);

        return redirect()->back()->with('success', 'Order completed!');
    }

    public function archiveCompleted()
    {
        try {
            // Archive completed orders from last 2 hours
            $completedCount = Order::where('status', 'completed')
                ->where('completed_at', '>=', now()->subHours(2))
                ->update(['status' => 'archived']);

            // Archive cancelled orders from last 2 hours
            $cancelledCount = Order::where('status', 'cancelled')
                ->where('updated_at', '>=', now()->subHours(2))
                ->update(['status' => 'archived']);

            $totalArchived = $completedCount + $cancelledCount;

            Log::info('Kitchen - Orders archived', [
                'completed_count' => $completedCount,
                'cancelled_count' => $cancelledCount,
                'total' => $totalArchived
            ]);

            return redirect()->back()->with('success', "Archived {$totalArchived} orders!");
        } catch (\Exception $e) {
            Log::error('Archive failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to archive orders');
        }
    }
}
