<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Models\{Order, OrderItem, MenuItem, Category, Inventory, Ingredient};
use Illuminate\Support\Facades\{DB, Session, Log};

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
     * Display the dashboard
     */
    public function dashboard()
    {
         $ingredients = Ingredient::all();
        return view('dashboard', compact('ingredients'));
    }

    /**
     * Display the products page
     */
    public function product(): View
    {
        $menu_items = MenuItem::all();
        return view('profile.product', compact('menu_items'));
    }

    /**
     * Store a new menu item via AJAX
     */
    public function storeMenuItem(Request $request)
{
    try {
        Log::info('=== storeMenuItem called ===');
        Log::info('Request data:', $request->all());
        
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0'
        ]);

        Log::info('Validation passed');

        // Get the first available category or create a default one
        $defaultCategory = Category::first();
        if (!$defaultCategory) {
            Log::info('No categories found, creating default category');
            // Create a default category using your exact fillable fields
            $defaultCategory = Category::create([
                'name' => 'Uncategorized',
                'description' => 'Default category for new menu items',
                'image' => null,
                'is_active' => true,  // Using boolean as per your casts
                'sort_order' => 999
            ]);
            Log::info('Default category created:', ['category' => $defaultCategory]);
        }

        Log::info('Using category:', ['category_id' => $defaultCategory->id]);

        $item = MenuItem::create([
            'name' => $request->name,
            'price' => $request->price,
            'cost' => $request->price * 0.6,
            'category_id' => $defaultCategory->id,
            'description' => '',
            'is_available' => 1,
            'preparation_time' => 10
        ]);
        
        Log::info('MenuItem created successfully:', ['item' => $item]);
        
        return response()->json([
            'success' => true, 
            'item' => $item,
            'message' => 'Menu item added successfully!'
        ]);
        
    } catch (\Illuminate\Validation\ValidationException $e) {
        Log::error('Validation failed:', ['errors' => $e->errors()]);
        return response()->json([
            'success' => false,
            'message' => 'Validation failed',
            'errors' => $e->errors()
        ], 422);
        
    } catch (\Exception $e) {
        Log::error('storeMenuItem error:', [
            'message' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString()
        ]);
        
        return response()->json([
            'success' => false,
            'message' => 'Server error: ' . $e->getMessage()
        ], 500);
    }
}

    /**
     * Update menu item price via AJAX
     */
    public function updateMenuItem(Request $request)
    {
        try {
            Log::info('=== updateMenuItem called ===');
            Log::info('Request data:', $request->all());
            
            $request->validate([
                'id' => 'required|exists:menu_items,id',
                'price' => 'required|numeric|min:0'
            ]);

            $item = MenuItem::find($request->id);
            $item->update(['price' => $request->price]);
            
            Log::info('MenuItem updated successfully');
            
            return response()->json([
                'success' => true,
                'message' => 'Menu item price updated successfully!'
            ]);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation failed:', ['errors' => $e->errors()]);
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
            
        } catch (\Exception $e) {
            Log::error('updateMenuItem error:', ['error' => $e->getMessage()]);
            
            return response()->json([
                'success' => false,
                'message' => 'Server error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete menu item via AJAX
     */
    public function deleteMenuItem(Request $request)
    {
        try {
            Log::info('=== deleteMenuItem called ===');
            Log::info('Request data:', $request->all());
            
            $request->validate([
                'id' => 'required|exists:menu_items,id'
            ]);

            $item = MenuItem::find($request->id);
            $itemName = $item->name;
            $item->delete();
            
            Log::info('MenuItem deleted successfully');
            
            return response()->json([
                'success' => true,
                'message' => "Menu item '{$itemName}' deleted successfully!"
            ]);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation failed:', ['errors' => $e->errors()]);
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
            
        } catch (\Exception $e) {
            Log::error('deleteMenuItem error:', ['error' => $e->getMessage()]);
            
            return response()->json([
                'success' => false,
                'message' => 'Server error: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Handle dine-in selection
     */
    public function dineIn(Request $request)
    {
        Session::put('order_type', 'dine-in');
        return redirect()->route('kiosk.main');
    }

    /**
     * Handle take-out selection
     */
    public function takeOut(Request $request)
    {
        Session::put('order_type', 'take-out');
        return redirect()->route('kiosk.main');
    }

    /**
     * Display the main menu page
     */
    public function main(Request $request)
    {
        // Get order type from session or default to 'dine-in'
        $orderType = Session::get('order_type', 'dine-in');
        
        // Get all categories with their menu items
        $categories = Category::where('is_active', 1)
            ->orderBy('sort_order')
            ->get();
        
        // Get all menu items with their categories, ordered by category
        // Filter out items without categories to prevent null reference errors
        $menuItems = MenuItem::with(['category', 'variants'])
            ->where('is_available', 1)
            ->whereHas('category') // This ensures only items with categories are included
            ->get()
            ->filter(function($item) {
                return $item->category !== null; // Additional safety check
            })
            ->sortBy(function($item) {
                return [$item->category->sort_order ?? 999, $item->name];
            });
        
        // Group menu items by category for easier frontend handling
        $itemsByCategory = $menuItems->groupBy('category.name');
        
        return view('kioskMain', compact('categories', 'menuItems', 'itemsByCategory', 'orderType'));
    }

    public function getCategoryItems($categoryId)
    {
        $menuItems = MenuItem::where('category_id', $categoryId)
                    ->where('is_available', true)
                    ->get();
    
        return response()->json(['menuItems' => $menuItems]);
    }
    

    //Update order type
    public function updateOrderType(Request $request)
    {
        $orderType = $request->input('order_type', 'dine-in');
        Session::put('order_type', $orderType);
        
        return response()->json(['status' => 'success', 'order_type' => $orderType]);
    }

    /**
     * Display the place order page
     */
    public function placeOrder(Request $request)
    {
        // Get cart items from session
        $cartItems = session('cart', []);
        $orderType = session('order_type', 'dine_in');
        
        // Calculate totals
        $subtotal = 0;
        foreach ($cartItems as $item) {
            $menuItem = MenuItem::find($item['menu_item_id']);
            if ($menuItem) {
                $subtotal += $menuItem->price * $item['quantity'];
            }
        }
        
        $tax = $subtotal * 0.10; // 10% tax
        $total = $subtotal + $tax;
        
        return view('kioskPlaceOrder', compact('cartItems', 'orderType', 'subtotal', 'tax', 'total'));
    }

    /**
     * Add item to cart
     */
    public function addToCart(Request $request)
    {
        $request->validate([
            'menu_item_id' => 'required|exists:menu_items,id',
            'quantity' => 'required|integer|min:1',
            'special_instructions' => 'nullable|string|max:255'
        ]);

        $cart = session('cart', []);
        $itemKey = $request->input('menu_item_id') . '_' . md5($request->input('special_instructions') ?? '');

        if (isset($cart[$itemKey])) {
            $cart[$itemKey]['quantity'] += $request->input('quantity');
        } else {
            $cart[$itemKey] = [
                'menu_item_id' => $request->input('menu_item_id'),
                'quantity' => $request->input('quantity'), // Use input() method
                'special_instructions' => $request->input('special_instructions')
            ];
        }

        session(['cart' => $cart]);

        return response()->json([
            'success' => true,
            'message' => 'Item added to cart',
            'cart_count' => array_sum(array_column($cart, 'quantity'))
        ]);
    }

    /**
     * Remove item from cart
     */
    public function removeFromCart(Request $request)
    {
        $itemKey = $request->input('item_key');
        $cart = session('cart', []);

        if (isset($cart[$itemKey])) {
            unset($cart[$itemKey]);
            session(['cart' => $cart]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Item removed from cart',
            'cart_count' => array_sum(array_column($cart, 'quantity'))
        ]);
    }

    
    /**
     * Submit the order
     */
    public function submitOrder(Request $request)
    {
        $request->validate([
            'table_number' => 'nullable|integer|min:1|max:50',
            'customer_name' => 'nullable|string|max:100'
        ]);

        $cart = session('cart', []);
        $orderType = session('order_type', 'dine_in');

        if (empty($cart)) {
            return redirect()->back()->with('error', 'Your cart is empty.');
        }

        try {
            DB::beginTransaction();

            // Calculate totals
            $subtotal = 0;
            foreach ($cart as $item) {
                $menuItem = MenuItem::find($item['menu_item_id']);
                if ($menuItem) {
                    $subtotal += $menuItem->price * $item['quantity'];
                }
            }

            $tax = $subtotal * 0.10;
            $total = $subtotal + $tax;

            // Create the order using your actual database columns
            $order = Order::create([
                'subtotal' => $subtotal,
                'tax_amount' => $tax,
                'discount_amount' => 0.00,
                'total_amount' => $total,
                'payment_method' => 'cash',
                'payment_status' => 'pending',
                'status' => 'pending',
                'notes' => "Order Type: {$orderType}" . ($orderType === 'dine_in' && $request->input('table_number') ? ", Table: {$request->input('table_number')}" : '') . ($request->input('customer_name') ? ", Customer: {$request->input('customer_name')}" : ''),
                'created_at' => now()
            ]);

            // Create order items using your actual database columns
            foreach ($cart as $item) {
                $menuItem = MenuItem::find($item['menu_item_id']);
                if ($menuItem) {
                    OrderItem::create([
                        'order_id' => $order->id,
                        'menu_item_id' => $item['menu_item_id'],
                        'quantity' => $item['quantity'],
                        'unit_price' => $menuItem->price,
                        'total_price' => $menuItem->price * $item['quantity'],
                        'special_instructions' => $item['special_instructions'],
                        'status' => 'pending'
                    ]);
                }
            }

            DB::commit();

            // Clear the cart and order type from session
            session()->forget(['cart', 'order_type']);

            return redirect()->route('kiosk.orderConfirmation', $order->id)
                ->with('success', 'Order placed successfully!');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Failed to place order. Please try again.');
        }
    }

    /**
     * Display order confirmation
     */
    public function orderConfirmation($orderId)
    {
        $order = Order::with(['orderItems.menuItem'])->findOrFail($orderId);
        
        return view('kioskOrderConfirmation', compact('order'));
    }

    /**
     * Display kitchen screen
     */
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

        // Add calculated fields for display
        foreach ($pendingOrders as $order) {
            $order->order_type = $this->extractOrderType($order->notes);
            $order->table_number = $this->extractTableNumber($order->notes);
            $order->customer_name = $this->extractCustomerName($order->notes);
            $order->estimated_prep_time = $this->calculatePrepTime($order->orderItems);
        }

        foreach ($processingOrders as $order) {
            $order->order_type = $this->extractOrderType($order->notes);
            $order->table_number = $this->extractTableNumber($order->notes);
            $order->customer_name = $this->extractCustomerName($order->notes);
            $order->started_at = $order->updated_at; // Use updated_at as started_at
        }

        // If this is an AJAX request, return only the data
        if (request()->ajax()) {
            return view('kitchen', compact('pendingOrders', 'processingOrders'));
        }

        return view('kitchen', compact('pendingOrders', 'processingOrders'));
    }

    /**
     * Start processing an order
     */
    public function startOrder($orderId)
    {
        $order = Order::findOrFail($orderId);
        
        $order->update([
            'status' => 'processing',
            'started_at' => now()
        ]);

        return redirect()->route('kitchen.index')->with('success', 'Order started!');
    }

    /**
     * Complete an order
     */
    public function completeOrder($orderId)
    {
        $order = Order::findOrFail($orderId);
        
        $order->update([
            'status' => 'completed',
            'completed_at' => now()
        ]);

        return redirect()->route('kitchen.index')->with('success', 'Order completed!');
    }

    /**
     * Calculate estimated preparation time based on cart items or order items
     */
    private function calculatePrepTime($items)
    {
        $totalTime = 0;
        $itemCount = 0;

        foreach ($items as $item) {
            if (is_array($item)) {
                // Cart item
                $menuItem = MenuItem::find($item['menu_item_id']);
                if ($menuItem) {
                    $baseTime = $menuItem->preparation_time ?? 5;
                    $totalTime += $baseTime * $item['quantity'];
                    $itemCount += $item['quantity'];
                }
            } else {
                // Order item (from database)
                $baseTime = $item->menuItem->preparation_time ?? 5;
                $totalTime += $baseTime * $item->quantity;
                $itemCount += $item->quantity;
            }
        }

        // Apply some logic to calculate realistic prep time
        $estimatedTime = max(15, min(45, $totalTime + ($itemCount * 2)));

        return $estimatedTime;
    }

    /**
     * Extract order type from notes
     */
    private function extractOrderType($notes)
    {
        if (strpos($notes, 'Order Type: dine_in') !== false) {
            return 'dine-in';
        } elseif (strpos($notes, 'Order Type: take_out') !== false) {
            return 'takeout';
        }
        return 'dine-in'; // default
    }

    /**
     * Extract table number from notes
     */
    private function extractTableNumber($notes)
    {
        if (preg_match('/Table: (\d+)/', $notes, $matches)) {
            return $matches[1];
        }
        return null;
    }

    /**
     * Extract customer name from notes
     */
    private function extractCustomerName($notes)
    {
        if (preg_match('/Customer: ([^,]+)/', $notes, $matches)) {
            return trim($matches[1]);
        }
        return null;
    }

    /**
     * Get cart contents (for AJAX)
     */
    public function getCart()
    {
        $cart = session('cart', []);
        $cartWithDetails = [];
        
        foreach ($cart as $key => $item) {
            $menuItem = MenuItem::find($item['menu_item_id']);
            if ($menuItem) {
                $cartWithDetails[$key] = array_merge($item, [
                    'name' => $menuItem->name,
                    'price' => $menuItem->price,
                    'subtotal' => $menuItem->price * $item['quantity']
                ]);
            }
        }

        return response()->json([
            'cart' => $cartWithDetails,
            'cart_count' => array_sum(array_column($cart, 'quantity'))
        ]);
    }
}