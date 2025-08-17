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
     * Process payment for both GCash and Cash
     */
    public function processPayment(Request $request)
    {
        try {
            $paymentMethod = $request->input('payment_method');

            if ($paymentMethod === 'gcash') {
                // Handle GCash payment via PayMongo
                return $this->processGCashPayment($request);
            } elseif ($paymentMethod === 'cash') {
                // Handle cash payment
                return $this->processCashPayment($request);
            }

            return response()->json([
                'success' => false,
                'message' => 'Invalid payment method'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Payment processing failed: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Process GCash payment via PayMongo - KEEPS 10% TAX FOR ONLINE PAYMENTS
     */
    private function processGCashPayment(Request $request)
    {
        try {
            $cart = Session::get('cart', []);
            $orderType = Session::get('orderType', 'dine-in');

            if (empty($cart)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cart is empty'
                ]);
            }

            // Calculate totals - ONLINE PAYMENT: ADD 10% TAX
            $subtotal = 0;
            foreach ($cart as $item) {
                $itemPrice = $item['price'] + ($item['addonsPrice'] ?? 0);
                $subtotal += $itemPrice * $item['quantity'];
            }

            $tax = $subtotal * 0.10; // 10% tax for online payments
            $total = $subtotal + $tax;

            Log::info('GCash payment calculation with tax:', [
                'subtotal' => $subtotal,
                'tax' => $tax,
                'total' => $total,
                'payment_method' => 'gcash'
            ]);

            // Save order to database first
            DB::beginTransaction();

            $order = Order::create([
                'subtotal' => $subtotal,
                'tax_amount' => $tax,
                'discount_amount' => 0.00,
                'total_amount' => $total,
                'payment_method' => 'gcash',
                'payment_status' => 'pending',
                'status' => 'pending',
                'order_type' => $orderType,
                'notes' => "Kiosk order - Type: {$orderType} (Online payment with 10% tax)",
                'created_at' => now()
            ]);

            // Create order items
            foreach ($cart as $item) {
                $itemPrice = $item['price'] + ($item['addonsPrice'] ?? 0);

                OrderItem::create([
                    'order_id' => $order->id,
                    'menu_item_id' => $item['menu_item_id'] ?? null,
                    'name' => $item['name'] ?? 'Custom Item',
                    'quantity' => $item['quantity'],
                    'unit_price' => $itemPrice,
                    'total_price' => $itemPrice * $item['quantity'],
                    'special_instructions' => isset($item['addons']) ? json_encode($item['addons']) : null,
                    'status' => 'pending'
                ]);
            }

            DB::commit();

            // Clear cart session
            Session::forget('cart');
            Session::put('last_order_id', $order->id);

            // Generate order number
            $orderNumber = 'C' . str_pad($order->id, 3, '0', STR_PAD_LEFT);

            // For now, simulate PayMongo checkout URL
            $checkoutUrl = route('kiosk.orderConfirmation', $order->id) . '?payment=gcash';

            return response()->json([
                'success' => true,
                'checkout_url' => $checkoutUrl,
                'order_id' => $order->id,
                'order_number' => $orderNumber,
                'total_amount' => $total, // Includes 10% tax
                'message' => 'GCash payment initiated'
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('GCash payment failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'GCash payment failed: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Process cash payment - TAX-INCLUSIVE PRICING
     */
    private function processCashPayment(Request $request)
    {
        try {
            $cart = Session::get('cart', []);
            $orderType = Session::get('orderType', 'dine-in');
            $cashAmount = floatval($request->input('cash_amount', 0));
            $changeAmount = floatval($request->input('change_amount', 0));

            Log::info('Processing cash payment', [
                'cash_amount' => $cashAmount,
                'change_amount' => $changeAmount,
                'cart_items' => count($cart)
            ]);

            if (empty($cart)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cart is empty'
                ]);
            }

            DB::beginTransaction();

            // Calculate totals - CASH PAYMENT: TAX-INCLUSIVE
            $subtotal = 0;
            foreach ($cart as $item) {
                $itemPrice = $item['price'] + ($item['addonsPrice'] ?? 0);
                $subtotal += $itemPrice * $item['quantity'];
            }

            // For CASH payments: prices are tax-inclusive
            // The displayed price IS the final price customer pays
            $tax = 0; // No additional tax for cash (already included in price)
            $total = $subtotal; // Total equals displayed prices

            // Recalculate change to ensure accuracy
            $actualChange = $cashAmount - $total;

            Log::info('Cash payment calculations (TAX-INCLUSIVE)', [
                'subtotal' => $subtotal,
                'tax' => $tax,
                'total' => $total,
                'cash_amount' => $cashAmount,
                'calculated_change' => $actualChange,
                'payment_method' => 'cash',
                'note' => 'Prices are tax-inclusive for cash payments'
            ]);

            // Create the order WITH cash_amount and change_amount
            $order = Order::create([
                'subtotal' => $subtotal,
                'tax_amount' => $tax, // 0 for cash (tax already included)
                'discount_amount' => 0.00,
                'total_amount' => $total, // Exact amount displayed to customer
                'payment_method' => 'cash',
                'payment_status' => 'pending',
                'status' => 'pending',
                'cash_amount' => $cashAmount,        // Save cash amount
                'change_amount' => $actualChange,    // Save change amount
                'order_type' => $orderType,          // Save order type in proper field
                'notes' => "Kiosk order - Type: {$orderType} (Tax-inclusive pricing)",
                'created_at' => now()
            ]);

            Log::info('Order created with cash details', [
                'order_id' => $order->id,
                'cash_amount' => $order->cash_amount,
                'change_amount' => $order->change_amount,
                'total_amount' => $order->total_amount,
                'pricing_model' => 'tax-inclusive'
            ]);

            // Create order items
            foreach ($cart as $item) {
                $itemPrice = $item['price'] + ($item['addonsPrice'] ?? 0);

                OrderItem::create([
                    'order_id' => $order->id,
                    'menu_item_id' => $item['menu_item_id'] ?? null,
                    'name' => $item['name'] ?? 'Custom Item',  // Save item name
                    'quantity' => $item['quantity'],
                    'unit_price' => $itemPrice,
                    'total_price' => $itemPrice * $item['quantity'],
                    'special_instructions' => isset($item['addons']) ? json_encode($item['addons']) : null,
                    'status' => 'pending'
                ]);
            }

            DB::commit();

            // Generate order number
            $orderNumber = 'C' . str_pad($order->id, 3, '0', STR_PAD_LEFT);

            // Clear cart session
            Session::forget('cart');
            Session::put('last_order_id', $order->id);

            Log::info('Cash payment processed successfully', [
                'order_id' => $order->id,
                'order_number' => $orderNumber
            ]);

            return response()->json([
                'success' => true,
                'order_id' => $order->id,
                'order_number' => $orderNumber,
                'cash_amount' => $cashAmount,
                'change_amount' => $actualChange,
                'total_amount' => $total, // This should match the displayed price exactly
                'message' => 'Cash order processed successfully'
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Cash payment failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Cash payment failed: Please try again'
            ]);
        }
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
     * CHECKOUT - Store cart data in session and prepare for review
     */
    public function checkout(Request $request)
    {
        try {
            // Store cart data in session
            $cartData = $request->input('items', []);
            $orderType = $request->input('order_type', 'dine-in');

            // Store in session for review page
            Session::put('cart', $cartData);
            Session::put('orderType', $orderType);
            Session::put('checkout_data', [
                'subtotal' => $request->input('subtotal', 0),
                'tax_amount' => $request->input('tax_amount', 0),
                'discount_amount' => $request->input('discount_amount', 0),
                'total' => $request->input('total', 0)
            ]);

            Log::info('Checkout data stored:', [
                'cart_items' => count($cartData),
                'order_type' => $orderType,
                'subtotal' => $request->input('subtotal', 0)
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Cart saved successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Checkout error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error saving cart: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * REVIEW ORDER - Display the order review page
     */
    public function reviewOrder()
    {
        // Get cart from session
        $cart = Session::get('cart', []);
        $orderType = Session::get('orderType', 'dine-in');

        Log::info('Review order page accessed:', [
            'cart_items' => count($cart),
            'order_type' => $orderType
        ]);

        return view('kioskOrderConfirmation', compact('cart', 'orderType'));
    }

    /**
     * UPDATE CART ITEM - Update quantity of item in cart
     */
    public function updateCartItem(Request $request)
    {
        try {
            $cart = Session::get('cart', []);
            $index = $request->input('index');
            $change = $request->input('change');

            if (isset($cart[$index])) {
                $cart[$index]['quantity'] += $change;

                // Remove item if quantity becomes 0 or less
                if ($cart[$index]['quantity'] <= 0) {
                    unset($cart[$index]);
                    $cart = array_values($cart); // Re-index array
                }

                Session::put('cart', $cart);
            }

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            Log::error('Update cart item error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * REMOVE CART ITEM - Remove specific item from cart
     */
    public function removeCartItem(Request $request)
    {
        try {
            $cart = Session::get('cart', []);
            $index = $request->input('index');

            if (isset($cart[$index])) {
                unset($cart[$index]);
                $cart = array_values($cart); // Re-index array
                Session::put('cart', $cart);
            }

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            Log::error('Remove cart item error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * CANCEL ORDER - Clear all cart data and return to start
     */
    public function cancelOrder(Request $request)
    {
        try {
            Session::forget(['cart', 'orderType', 'checkout_data']);
            Log::info('Order cancelled - session data cleared');
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            Log::error('Cancel order error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * PROCESS ORDER - Final order processing and save to database
     */
    public function processOrder(Request $request)
    {
        try {
            $cart = Session::get('cart', []);
            $orderType = Session::get('orderType', 'dine-in');
            $paymentMethod = $request->input('payment_method', 'cash');

            if (empty($cart)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cart is empty'
                ]);
            }

            DB::beginTransaction();

            // Calculate totals based on payment method
            $subtotal = 0;
            foreach ($cart as $item) {
                $itemPrice = $item['price'] + ($item['addonsPrice'] ?? 0);
                $subtotal += $itemPrice * $item['quantity'];
            }

            // Apply different tax logic based on payment method
            if ($paymentMethod === 'cash') {
                // Cash: tax-inclusive pricing
                $tax = 0;
                $total = $subtotal;
            } else {
                // Online payments: add 10% tax
                $tax = $subtotal * 0.10;
                $total = $subtotal + $tax;
            }

            // Create the order
            $order = Order::create([
                'subtotal' => $subtotal,
                'tax_amount' => $tax,
                'discount_amount' => 0.00,
                'total_amount' => $total,
                'payment_method' => $paymentMethod,
                'payment_status' => 'pending',
                'status' => 'pending',
                'order_type' => $orderType,
                'notes' => "Kiosk order - Type: {$orderType}, Payment: {$paymentMethod}",
                'created_at' => now()
            ]);

            // Create order items
            foreach ($cart as $item) {
                $itemPrice = $item['price'] + ($item['addonsPrice'] ?? 0);

                OrderItem::create([
                    'order_id' => $order->id,
                    'menu_item_id' => $item['menu_item_id'] ?? null,
                    'name' => $item['name'] ?? 'Custom Item',
                    'quantity' => $item['quantity'],
                    'unit_price' => $itemPrice,
                    'total_price' => $itemPrice * $item['quantity'],
                    'special_instructions' => isset($item['addons']) ? json_encode($item['addons']) : null,
                    'status' => 'pending'
                ]);
            }

            DB::commit();

            // Clear cart data but keep order info for confirmation
            Session::forget(['cart', 'checkout_data']);
            Session::put('last_order_id', $order->id);

            Log::info('Order processed successfully:', [
                'order_id' => $order->id,
                'payment_method' => $paymentMethod,
                'total_amount' => $total
            ]);

            return response()->json([
                'success' => true,
                'redirect_url' => route('kiosk.orderConfirmation', $order->id)
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Process order error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error processing order: ' . $e->getMessage()
            ]);
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
        Log::info('Order type set to dine-in');
        return redirect()->route('kiosk.main');
    }

    /**
     * Handle take-out selection
     */
    public function takeOut(Request $request)
    {
        Session::put('order_type', 'take-out');
        Log::info('Order type set to take-out');
        return redirect()->route('kiosk.main');
    }

    /**
     * Display the main menu page
     */
    public function main(Request $request)
    {
        // Get order type from session or default to 'dine-in'
        $orderType = Session::get('order_type', 'dine-in');

        // Get all active categories
        $categories = Category::where('is_active', 1)
            ->orderBy('sort_order')
            ->get();

        // Get ALL available menu items with their categories
        // Remove the whereHas filter to include items without categories too
        $menuItems = MenuItem::with(['category', 'variants'])
            ->where('is_available', 1)
            ->get()
            ->sortBy(function ($item) {
                // Sort by category sort_order, then by item name
                $categoryOrder = (is_object($item->category) && isset($item->category->sort_order)) ? $item->category->sort_order : 999;
                return [$categoryOrder, $item->name];
            });

        // Group menu items by category for easier frontend handling
        $itemsByCategory = $menuItems->groupBy(fn($item) => $item->category && is_object($item->category) ? (string)$item->category->name : 'Uncategorized');

        Log::info('Main menu page loaded:', [
            'order_type' => $orderType,
            'categories_count' => $categories->count(),
            'menu_items_count' => $menuItems->count(),
            'items_with_categories' => $menuItems->where('category_id', '!=', null)->count(),
            'items_without_categories' => $menuItems->where('category_id', null)->count()
        ]);

        // Debug: Log items by category
        foreach ($categories as $category) {
            $categoryItems = $menuItems->where('category_id', $category->id);
            Log::info("Category '{$category->name}' (ID: {$category->id}) has {$categoryItems->count()} items");
        }

        return view('kioskMain', compact('categories', 'menuItems', 'itemsByCategory', 'orderType'));
    }
    /**
     * Get category items via AJAX
     */
    public function getCategoryItems($categoryId)
    {
        try {
            Log::info('getCategoryItems called', ['category_id' => $categoryId]);

            // Get menu items with category relationship
            $menuItems = MenuItem::with('category')
                ->where('category_id', $categoryId)
                ->where('is_available', true)
                ->get();

            Log::info('Found menu items', [
                'category_id' => $categoryId,
                'count' => $menuItems->count(),
                'items' => $menuItems->pluck('name', 'id')->toArray()
            ]);

            // Transform the data to include proper image URLs
            $transformedItems = $menuItems->map(function ($item) {
                return [
                    'id' => $item->id,
                    'name' => $item->name,
                    'price' => $item->price,
                    'description' => $item->description,
                    'image' => $item->image,
                    'category_id' => $item->category_id,
                    'category' => $item->category,
                    'has_variants' => $item->has_variants ?? false,
                    'is_available' => $item->is_available
                ];
            });

            return response()->json([
                'success' => true,
                'menuItems' => $transformedItems
            ]);
        } catch (\Exception $e) {
            Log::error('getCategoryItems error:', [
                'category_id' => $categoryId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error fetching category items: ' . $e->getMessage(),
                'menuItems' => []
            ], 500);
        }
    }

    /**
     * HELPER METHOD: Assign uncategorized items to a default category
     */
    public function fixUncategorizedItems()
    {
        try {
            // Find or create an "Uncategorized" category
            $uncategorizedCategory = Category::firstOrCreate(
                ['name' => 'Uncategorized'],
                [
                    'description' => 'Items without a specific category',
                    'image' => null,
                    'is_active' => true,
                    'sort_order' => 999
                ]
            );

            // Update all items without categories
            $uncategorizedCount = MenuItem::whereNull('category_id')
                ->update(['category_id' => $uncategorizedCategory->id]);

            Log::info('Fixed uncategorized items', [
                'uncategorized_category_id' => $uncategorizedCategory->id,
                'items_updated' => $uncategorizedCount
            ]);

            return response()->json([
                'success' => true,
                'message' => "Fixed {$uncategorizedCount} uncategorized items",
                'category_id' => $uncategorizedCategory->id
            ]);
        } catch (\Exception $e) {
            Log::error('Fix uncategorized items error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error fixing uncategorized items: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * DEBUG METHOD: Get category and item information
     */
    public function debugCategoriesAndItems()
    {
        $categories = Category::with(['menuItems' => function ($query) {
            $query->where('is_available', 1);
        }])->where('is_active', 1)->get();

        $debugInfo = [];
        foreach ($categories as $category) {
            $debugInfo[] = [
                'category_id' => $category->id,
                'category_name' => $category->name,
                'item_count' => $category->menuItems->count(),
                'items' => $category->menuItems->pluck('name', 'id')->toArray()
            ];
        }

        // Also check items without categories
        $uncategorizedItems = MenuItem::whereNull('category_id')
            ->where('is_available', 1)
            ->get();

        $debugInfo[] = [
            'category_id' => null,
            'category_name' => 'Uncategorized',
            'item_count' => $uncategorizedItems->count(),
            'items' => $uncategorizedItems->pluck('name', 'id')->toArray()
        ];

        Log::info('Categories and Items Debug Info:', $debugInfo);

        return response()->json([
            'success' => true,
            'debug_info' => $debugInfo
        ]);
    }


    /**
     * Update order type via AJAX
     */
    public function updateOrderType(Request $request)
    {
        $orderType = $request->input('order_type', 'dine-in');
        Session::put('order_type', $orderType);

        Log::info('Order type updated:', ['order_type' => $orderType]);

        return response()->json(['status' => 'success', 'order_type' => $orderType]);
    }

    /**
     * Display the place order page
     */
    public function placeOrder(Request $request)
    {
        // Redirect to the review order page since you already have that implemented
        return redirect()->route('kiosk.reviewOrder');
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

        Log::info('Item added to cart:', [
            'menu_item_id' => $request->input('menu_item_id'),
            'quantity' => $request->input('quantity'),
            'cart_total_items' => array_sum(array_column($cart, 'quantity'))
        ]);

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
            Log::info('Item removed from cart:', ['item_key' => $itemKey]);
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

            // For submitted orders, assume cash payment (tax-inclusive)
            $tax = 0;
            $total = $subtotal;

            // Create the order using your actual database columns
            $order = Order::create([
                'subtotal' => $subtotal,
                'tax_amount' => $tax,
                'discount_amount' => 0.00,
                'total_amount' => $total,
                'payment_method' => 'cash',
                'payment_status' => 'pending',
                'status' => 'pending',
                'order_type' => $orderType,
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
                        'name' => $menuItem->name,
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

            Log::info('Order submitted successfully:', [
                'order_id' => $order->id,
                'total_amount' => $total
            ]);

            return redirect()->route('kiosk.orderConfirmation', $order->id)
                ->with('success', 'Order placed successfully!');
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Submit order error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to place order. Please try again.');
        }
    }

    /**
     * Display order confirmation
     */
    public function orderConfirmation($orderId = null)
    {
        if ($orderId) {
            $order = Order::with(['orderItems.menuItem'])->findOrFail($orderId);
            Log::info('Order confirmation displayed:', ['order_id' => $orderId]);
            // Use the SUCCESS confirmation view
            return view('orderConfirmationSuccess', compact('order'));
        } else {
            // Handle case where no order ID is provided (from processOrder)
            $lastOrderId = Session::get('last_order_id');
            if ($lastOrderId) {
                $order = Order::with(['orderItems.menuItem'])->findOrFail($lastOrderId);
                Session::forget('last_order_id');
                Log::info('Order confirmation displayed from session:', ['order_id' => $lastOrderId]);
                // Use the SUCCESS confirmation view
                return view('orderConfirmationSuccess', compact('order'));
            }
        }

        Log::warning('Order confirmation accessed without valid order ID');
        return redirect()->route('kiosk.index')->with('error', 'Order not found');
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

        Log::info('Order started:', ['order_id' => $orderId]);

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

        Log::info('Order completed:', ['order_id' => $orderId]);

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
        if (strpos($notes, 'Order Type: dine_in') !== false || strpos($notes, 'dine-in') !== false) {
            return 'dine-in';
        } elseif (strpos($notes, 'Order Type: take_out') !== false || strpos($notes, 'take-out') !== false) {
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
