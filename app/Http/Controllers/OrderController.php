<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Mail\OrderReceiptMail;
use Exception;

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

    public function sendReceipt(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'order_id' => 'required',
        ]);

        try {
            // Get order details
            $order = Order::find($validated['order_id']);

            if (!$order) {
                return response()->json([
                    'success' => false,
                    'message' => 'Order not found'
                ], 404);
            }

            // Send email using Laravel Mail
            Mail::to($validated['email'])->send(new OrderReceiptMail($order));

            return response()->json([
                'success' => true,
                'message' => 'Receipt sent successfully'
            ]);
        } catch (Exception $e) {
            // Log the error for debugging
            Log::error('Failed to send receipt email', [
                'error' => $e->getMessage(),
                'order_id' => $validated['order_id'],
                'email' => $validated['email']
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to send receipt'
            ], 500);
        }
    }


    public function completeOrder($id)
    {
        try {
            DB::beginTransaction();

            $order = Order::findOrFail($id);
            $order->update([
                'status' => 'completed',
                'completed_at' => now()
            ]);

            // Deduct inventory for each order item
            foreach ($order->orderItems as $orderItem) {
                $this->deductInventoryForOrderItem($orderItem);
            }

            DB::commit();

            return redirect()->route('kitchen.index')->with('success', 'Order completed and inventory updated!');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error completing order: ' . $e->getMessage());
            return redirect()->route('kitchen.index')->with('error', 'Error completing order');
        }
    }

    /**
     * Handle AJAX completion requests
     */
    public function markCompleted(Request $request, $orderId)
    {
        try {
            DB::beginTransaction();

            $order = Order::findOrFail($orderId);
            $order->update([
                'status' => 'completed',
                'completed_at' => now()
            ]);

            // Deduct inventory for each order item
            foreach ($order->orderItems as $orderItem) {
                $this->deductInventoryForOrderItem($orderItem);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Order completed and inventory updated'
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error completing order: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error completing order: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update order status (for AJAX requests)
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,processing,completed'
        ]);

        try {
            $order = Order::findOrFail($id);
            $order->status = $request->status;

            if ($request->status === 'processing') {
                $order->started_at = now();
            } elseif ($request->status === 'completed') {
                $order->completed_at = now();
            }

            $order->save();

            return response()->json([
                'success' => true,
                'message' => 'Order status updated successfully'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating order status: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Deduct inventory for a single order item
     */
    private function deductInventoryForOrderItem($orderItem)
    {
        // Get all ingredients for this menu item
        $menuItemIngredients = DB::table('menu_item_ingredients')
            ->where('menu_item_id', $orderItem->menu_item_id)
            ->get();

        foreach ($menuItemIngredients as $ingredient) {
            // Calculate total quantity to deduct
            $totalDeduct = $ingredient->quantity_needed * $orderItem->quantity;

            // Update ingredient stock
            DB::table('ingredients')
                ->where('id', $ingredient->ingredient_id)
                ->decrement('stock_quantity', $totalDeduct);

            // Log the transaction (if stock_transactions table exists)
            try {
                DB::table('stock_transactions')->insert([
                    'ingredient_id' => $ingredient->ingredient_id,
                    'transaction_type' => 'usage',
                    'quantity' => -$totalDeduct,
                    'reference_type' => 'order_item',
                    'reference_id' => $orderItem->id,
                    'notes' => "Order #{$orderItem->order->order_number} - {$orderItem->menuItem->name}",
                    'created_at' => now()
                ]);
            } catch (Exception $e) {
                // If stock_transactions table doesn't exist, just log the error but continue
                Log::info('Stock transaction logging skipped: ' . $e->getMessage());
            }
        }
    }
}
