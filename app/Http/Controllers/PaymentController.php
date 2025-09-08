<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Inventory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{

    public function processCashPayment(Request $request)
    {
        DB::beginTransaction();

        try {
            $orderData = $request->all();

            // Get cart items from session
            $cartItems = session('cart', []);


            // Create order
            $order = Order::create([
                'order_number' => $this->generateOrderNumber(),
                'order_type' => session('order_type', 'takeout'),
                'table_number' => $orderData['tableNumber'] ?? null,
                'total_amount' => $orderData['cash_amount'] - ($orderData['change_amount'] ?? 0),
                'payment_method' => 'cash',
                'cash_amount' => $orderData['cash_amount'],
                'change_amount' => $orderData['change_amount'] ?? 0,
                'status' => 'pending',
                'estimated_prep_time' => 15 // minutes
            ]);

            // Create order items
            foreach ($cartItems as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'menu_item_id' => $item['menu_item_id'] ?? null,
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['price'] ?? 0,
                    'total_price' => ($item['price'] ?? 0) * $item['quantity'],
                    'special_instructions' => isset($item['modifiers']) && is_array($item['modifiers']) ? implode(', ', $item['modifiers']) : null,
                    'status' => 'pending'
                ]);
            }

            $this->deductPackagingSupplies($cartItems, $order->order_type);

            // Track ingredient usage properly
            foreach ($cartItems as $item) {
                $ingredientsUsed = DB::select("
        SELECT mii.ingredient_id, mii.quantity_needed * ? as used_amount
        FROM menu_item_ingredients mii
        WHERE mii.menu_item_id = ?
    ", [$item['quantity'], $item['menu_item_id']]);

                foreach ($ingredientsUsed as $ingredient) {
                    $inventory = Inventory::where('menu_item_id', $ingredient->ingredient_id)->first();
                    if ($inventory) {
                        Log::info("Before deduction", [
                            'ingredient_id' => $ingredient->ingredient_id,
                            'used_amount' => $ingredient->used_amount,
                            'current_stock_before' => $inventory->current_stock
                        ]);

                        $inventory->used_stock = ($inventory->used_stock ?? 0) + $ingredient->used_amount;
                        $inventory->current_stock = max(0, $inventory->current_stock - $ingredient->used_amount);
                        $inventory->save();

                        Log::info("After deduction", [
                            'current_stock_after' => $inventory->current_stock,
                            'used_stock' => $inventory->used_stock
                        ]);
                    }
                }
            }

            // Clear cart after successful order
            session()->forget(['cart', 'order_type', 'table_number']);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Payment processed successfully',
                'order_id' => $order->id,
                'order_number' => $order->order_number
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Payment processing failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Payment processing failed: ' . $e->getMessage()
            ], 500);
        }
    }

    private function generateOrderNumber()
    {
        $lastOrder = Order::latest()->first();
        return $lastOrder ? $lastOrder->id + 1 : 1001;
    }
    /**
     * Deduct ingredients for an order item
     */




    /**
     * Deduct packaging supplies for takeout orders
     */
    private function deductPackagingSupplies($orderItems, $orderType)
    {
        if ($orderType !== 'take-out') {
            return;
        }

        Log::info('Starting packaging supplies deduction', [
            'order_type' => $orderType,
            'cart_items' => count($orderItems),
        ]);

        // Calculate total items
        $totalItemsCount = 0;
        foreach ($orderItems as $item) {
            $totalItemsCount += $item['quantity'];
        }

        // Check if packaging supplies exist first
        $packagingItems = DB::table('ingredients')
            ->where('category', 'packaging-supplies')
            ->pluck('name', 'id');

        Log::info('Available packaging supplies:', $packagingItems->toArray());

        if ($packagingItems->isEmpty()) {
            Log::warning('No packaging supplies found in inventory');
            return;
        }

        // Only deduct packaging supplies for takeout orders
        if ($orderType !== 'take-out') {
            return;
        }

        Log::info('Deducting packaging supplies for takeout order', [
            'order_type' => $orderType,
            'items_count' => count($orderItems)
        ]);

        // Define packaging supplies mapping
        $packagingSupplies = [
            'Disposable Food Containers' => 1, // 1 container per order item
            'Plastic Bags' => 1, // 1 bag per order (not per item)
            'Napkins' => 2, // 2 napkins per order item
            'Disposable Utensils' => 1, // 1 set of utensils per order item
        ];

        // Calculate total quantities needed
        $totalItemsCount = array_sum(array_column($orderItems, 'quantity'));
        $packagingNeeded = [];

        foreach ($packagingSupplies as $itemName => $qtyPerItem) {
            if ($itemName === 'Plastic Bags') {
                // Only 1 bag per order regardless of items
                $packagingNeeded[$itemName] = 1;
            } else {
                // Multiply by total item count
                $packagingNeeded[$itemName] = $qtyPerItem * $totalItemsCount;
            }
        }

        Log::info('Packaging supplies needed:', $packagingNeeded);

        // Deduct each packaging supply using FIFO method
        foreach ($packagingNeeded as $itemName => $quantityNeeded) {
            $this->deductPackagingItem($itemName, $quantityNeeded);
        }
    }

    /**
     * Deduct a specific packaging item using FIFO
     */
    private function deductPackagingItem($itemName, $quantityNeeded)
    {
        // Find packaging item in inventory table
        $packagingInventory = Inventory::whereHas('ingredient', function ($query) use ($itemName) {
            $query->where('name', $itemName)->where('category', 'packaging-supplies');
        })->first();

        if ($packagingInventory && $packagingInventory->current_stock >= $quantityNeeded) {
            $packagingInventory->used_stock = ($packagingInventory->used_stock ?? 0) + $quantityNeeded;
            $packagingInventory->current_stock = max(0, $packagingInventory->current_stock - $quantityNeeded);
            $packagingInventory->save();
        } else {
            Log::warning("Insufficient packaging supply: {$itemName}");
        }
    }
}
