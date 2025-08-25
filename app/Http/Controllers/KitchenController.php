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
        DB::beginTransaction();
        
        try {
            $order = Order::with('orderItems.menuItem')->findOrFail($orderId);
            
            // Deduct ingredients for each order item
            foreach ($order->orderItems as $orderItem) {
                $this->deductIngredients($orderItem->menuItem->id, $orderItem->quantity);
            }
            
            // Update order status
            $order->update([
                'status' => 'completed',
                'completed_at' => now()
            ]);
            
            DB::commit();
            
            return redirect()->back()->with('success', 'Order completed and ingredients updated!');
            
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Error completing order: ' . $e->getMessage());
        }
    }

    private function deductIngredients($menuItemId, $quantity)
    {
        // Get all ingredients required for this menu item
        $menuIngredients = MenuItemIngredient::with('ingredient')
            ->where('menu_item_id', $menuItemId)
            ->get();
        
        foreach ($menuIngredients as $menuIngredient) {
            $totalNeeded = $menuIngredient->quantity_needed * $quantity;
            
            // Get available ingredients with same name, ordered by created_at (FIFO)
            $ingredients = Ingredient::where('name', $menuIngredient->ingredient->name)
                ->where('stock_quantity', '>', 0)
                ->orderBy('created_at', 'asc')
                ->get();
            
            $remainingNeeded = $totalNeeded;
            
            foreach ($ingredients as $ingredient) {
                if ($remainingNeeded <= 0) break;
                
                if ($ingredient->stock_quantity >= $remainingNeeded) {
                    // This ingredient has enough stock
                    $ingredient->stock_quantity -= $remainingNeeded;
                    $ingredient->save();
                    $remainingNeeded = 0;
                } else {
                    // Use all of this ingredient and continue
                    $remainingNeeded -= $ingredient->stock_quantity;
                    $ingredient->stock_quantity = 0;
                    $ingredient->save();
                }
            }
            
            // Log if we couldn't fulfill the complete requirement
            if ($remainingNeeded > 0) {
                Log::warning("Insufficient stock for {$menuIngredient->ingredient->name}. Missing: {$remainingNeeded}");
            }
        }
    }
}