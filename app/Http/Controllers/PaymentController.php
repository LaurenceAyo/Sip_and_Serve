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
                $this->deductIngredients($item['menu_item_id'], $item['quantity']);
            }

            $this->deductPackagingSupplies($cartItems, $order->order_type);
            $this->syncInventoryDisplay();

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
                        $inventory->used_stock = ($inventory->used_stock ?? 0) + $ingredient->used_amount;
                        $inventory->save();
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
    private function deductIngredients($orderItemId, $quantity)
    {
        // Get all ingredients needed for this order item
        $ingredientsNeeded = DB::select("
        SELECT i.name as ingredient_name, mii.quantity_needed * ? as total_needed
        FROM menu_item_ingredients mii
        INNER JOIN ingredients i ON i.id = mii.ingredient_id
        WHERE mii.menu_item_id = ?
    ", [$quantity, $orderItemId]);

        foreach ($ingredientsNeeded as $ingredient) {
            $remainingNeeded = $ingredient->total_needed;

            // Deduct using FIFO (First In, First Out)
            while ($remainingNeeded > 0) {
                $availableIngredient = DB::table('ingredients')
                    ->where('name', $ingredient->ingredient_name)
                    ->where('stock_quantity', '>', 0)
                    ->orderBy('created_at', 'asc')
                    ->first();

                if (!$availableIngredient) {
                    // No more stock available
                    break;
                }

                if ($availableIngredient->stock_quantity >= $remainingNeeded) {
                    // This ingredient has enough stock
                    DB::table('ingredients')
                        ->where('id', $availableIngredient->id)
                        ->update([
                            'stock_quantity' => $availableIngredient->stock_quantity - $remainingNeeded,
                            'updated_at' => now()
                        ]);

                    $remainingNeeded = 0;
                } else {
                    // Use all of this ingredient and continue
                    DB::table('ingredients')
                        ->where('id', $availableIngredient->id)
                        ->update([
                            'stock_quantity' => 0,
                            'updated_at' => now()
                        ]);

                    $remainingNeeded -= $availableIngredient->stock_quantity;
                }
            }
        }
    }




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
        $remainingNeeded = $quantityNeeded;

        Log::info("Deducting packaging item: {$itemName}", [
            'quantity_needed' => $quantityNeeded
        ]);

        // Use FIFO (First In, First Out) method similar to ingredient deduction
        while ($remainingNeeded > 0) {
            $availableItem = DB::table('ingredients')
                ->where('name', $itemName)
                ->where('category', 'packaging-supplies')
                ->where('stock_quantity', '>', 0)
                ->orderBy('created_at', 'asc')
                ->first();

            if (!$availableItem) {
                // Log warning if packaging supply is out of stock
                Log::warning("Packaging supply out of stock: {$itemName}", [
                    'quantity_still_needed' => $remainingNeeded,
                    'original_quantity_needed' => $quantityNeeded
                ]);
                break;
            }

            if ($availableItem->stock_quantity >= $remainingNeeded) {
                // This batch has enough stock
                DB::table('ingredients')
                    ->where('id', $availableItem->id)
                    ->update([
                        'stock_quantity' => $availableItem->stock_quantity - $remainingNeeded,
                        'updated_at' => now()
                    ]);

                Log::info("Deducted {$remainingNeeded} {$itemName} from batch {$availableItem->id}");
                $remainingNeeded = 0;
            } else {
                // Use all of this batch and continue
                DB::table('ingredients')
                    ->where('id', $availableItem->id)
                    ->update([
                        'stock_quantity' => 0,
                        'updated_at' => now()
                    ]);

                Log::info("Used all {$availableItem->stock_quantity} {$itemName} from batch {$availableItem->id}");
                $remainingNeeded -= $availableItem->stock_quantity;
            }
        }
    }

    private function syncInventoryDisplay()
    {
        $inventoryItems = Inventory::all();
        foreach ($inventoryItems as $inventory) {
            // Find ingredient by name matching
            $ingredientName = $inventory->ingredient->name ?? null;
            if ($ingredientName) {
                $totalStock = DB::table('ingredients')
                    ->where('name', $ingredientName)
                    ->sum('stock_quantity');

                $inventory->current_stock = max(0, $totalStock);
                $inventory->save();
            }
        }
    }

    /*public function checkItemAvailability($menuItemId)
    {
        $ingredientsNeeded = DB::select("
        SELECT i.name, mii.quantity_needed
        FROM menu_item_ingredients mii
        INNER JOIN ingredients i ON i.id = mii.ingredient_id
        WHERE mii.menu_item_id = ?
    ", [$menuItemId]);

        foreach ($ingredientsNeeded as $ingredient) {
            $available = DB::table('ingredients')
                ->where('name', $ingredient->name)
                ->sum('stock_quantity');

            if ($available < $ingredient->quantity_needed) {
                return false; // Item unavailable
            }
        }
        return true; // Item available
    }*/
}
