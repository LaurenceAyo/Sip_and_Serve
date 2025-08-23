<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KitchenController extends Controller
{
    public function index()
    {
        // Get pending orders (either paid OR cash orders accepted by cashier)
        $pendingOrders = Order::with('orderItems.menuItem')
            ->where(function ($query) {
                $query->where('payment_status', 'paid')
                    ->orWhere(function ($subQuery) {
                        $subQuery->where('payment_method', 'cash')
                            ->where('payment_status', 'pending')
                            ->whereNotNull('cash_amount');
                    });
            })
            ->where('status', 'pending')
            ->latest()
            ->get();

        // Get orders currently being prepared
        $processingOrders = Order::with('orderItems.menuItem')
            ->where('status', 'preparing')
            ->latest()
            ->get();

        // Add calculated fields WITHOUT converting to arrays - keep as Order models
        $processingOrders = $processingOrders->map(function ($order) {
            if ($order->started_at) {
                $processingSeconds = now()->diffInSeconds($order->started_at);
                
                // Add dynamic properties directly to the model
                $order->processing_time_display = sprintf(
                    '%02d:%02d',
                    floor($processingSeconds / 60),
                    $processingSeconds % 60
                );

                // Check if overdue
                $estimatedSeconds = ($order->estimated_prep_time ?? 30) * 60;
                $order->is_overdue_calculated = $processingSeconds > $estimatedSeconds;
            } else {
                $order->processing_time_display = '00:00';
                $order->is_overdue_calculated = false;
            }
            
            return $order; // Return the Order model, not stdClass
        });

        // Get recently completed orders (last 2 hours)
        $completedOrders = Order::with('orderItems.menuItem')
            ->where('status', 'completed')
            ->where('completed_at', '>=', now()->subHours(2))
            ->latest('completed_at')
            ->take(10)
            ->get();

        // Add total prep time WITHOUT converting to arrays - keep as Order models
        $completedOrders = $completedOrders->map(function ($order) {
            if ($order->started_at && $order->completed_at) {
                $order->total_prep_time_calculated = $order->completed_at->diffInMinutes($order->started_at);
            } else {
                $order->total_prep_time_calculated = null;
            }
            
            return $order; // Return the Order model, not stdClass
        });

        return view('kitchen', compact('pendingOrders', 'processingOrders', 'completedOrders'));
    }

    // Start preparing an order
    public function start(Order $order)
    {
        // Calculate estimated prep time if not set
        if (!$order->estimated_prep_time) {
            $totalItems = $order->orderItems->sum('quantity');
            $order->estimated_prep_time = min(max($totalItems * 5 + 10, 15), 60);
            $order->save();
        }

        $estimatedCompletionTime = now()->addMinutes($order->estimated_prep_time);

        $order->update([
            'status' => 'preparing',
            'started_at' => now()
        ]);

        return redirect()->back()->with('success', 'Order started! Estimated completion: ' .
            $estimatedCompletionTime->format('g:i A'));
    }

    // Complete an order
    public function completeOrder(Order $order)
    {
        $completedAt = now();
        $order->update([
            'status' => 'completed',
            'completed_at' => $completedAt
        ]);

        // Record daily sales
        $this->recordDailySale($order);

        // Calculate actual prep time
        if ($order->started_at) {
            $actualPrepTime = $order->started_at->diffInMinutes($completedAt);
            $estimatedTime = $order->estimated_prep_time ?? 30;

            $message = "Order completed! ";
            $variance = $actualPrepTime - $estimatedTime;

            if ($variance > 5) {
                $message .= "Took {$variance} minutes longer than estimated.";
            } elseif ($variance < -5) {
                $message .= "Completed " . abs($variance) . " minutes faster than estimated!";
            } else {
                $message .= "Completed on time!";
            }
        } else {
            $message = "Order completed successfully!";
        }

        return redirect()->back()->with('success', $message);
    }

    // Record daily sales
    private function recordDailySale($order)
    {
        $today = now()->format('Y-m-d');

        // Find or create today's daily sales record
        $dailySale = DB::table('daily_sales')->where('date', $today)->first();

        if ($dailySale) {
            // Update existing record
            DB::table('daily_sales')
                ->where('date', $today)
                ->increment('total_orders', 1);

            DB::table('daily_sales')
                ->where('date', $today)
                ->increment('total_sales', $order->total_amount);

            // Update payment method specific columns
            if ($order->payment_method === 'cash') {
                DB::table('daily_sales')
                    ->where('date', $today)
                    ->increment('cash_sales', $order->total_amount);
            } elseif ($order->payment_method === 'card') {
                DB::table('daily_sales')
                    ->where('date', $today)
                    ->increment('card_sales', $order->total_amount);
            } elseif ($order->payment_method === 'gcash') {
                DB::table('daily_sales')
                    ->where('date', $today)
                    ->increment('digital_wallet_sales', $order->total_amount); // Fixed typo
            }
        } else {
            // Create new daily sales record
            $cashSales = $order->payment_method === 'cash' ? $order->total_amount : 0;
            $cardSales = $order->payment_method === 'card' ? $order->total_amount : 0;
            $digitalWalletSales = $order->payment_method === 'gcash' ? $order->total_amount : 0;

            DB::table('daily_sales')->insert([
                'date' => $today,
                'total_orders' => 1,
                'total_sales' => $order->total_amount,
                'cash_sales' => $cashSales,
                'card_sales' => $cardSales,
                'digital_wallet_sales' => $digitalWalletSales,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    // Get real-time kitchen data for AJAX updates
    public function getData()
    {
        $pendingOrders = Order::with('orderItems.menuItem')
            ->where(function ($query) {
                $query->where('payment_status', 'paid')
                    ->orWhere(function ($subQuery) {
                        $subQuery->where('payment_method', 'cash')
                            ->where('payment_status', 'pending')
                            ->whereNotNull('cash_amount');
                    });
            })
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