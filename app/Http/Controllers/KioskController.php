<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Models\{Order, OrderItem, MenuItem, Category, Inventory, Ingredient};
use Illuminate\Support\Facades\{DB, Session, Log};
use Exception;
use App\Services\PaymongoService;


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

    private function checkStockAvailability($cart)
    {
        $insufficientItems = [];

        foreach ($cart as $item) {
            $menuItemId = $item['menu_item_id'];
            $quantity = $item['quantity'];

            // Get ingredients needed
            $ingredientsNeeded = DB::select("
            SELECT i.name as ingredient_name, mii.quantity_needed * ? as total_needed
            FROM menu_item_ingredients mii
            INNER JOIN ingredients i ON i.id = mii.ingredient_id
            WHERE mii.menu_item_id = ?
        ", [$quantity, $menuItemId]);

            foreach ($ingredientsNeeded as $ingredient) {
                // Check total available stock
                $totalAvailable = DB::table('ingredients')
                    ->where('name', $ingredient->ingredient_name)
                    ->where('stock_quantity', '>', 0)
                    ->sum('stock_quantity');

                if ($totalAvailable < $ingredient->total_needed) {
                    $insufficientItems[] = [
                        'menu_item' => $item['name'] ?? 'Menu Item #' . $menuItemId,
                        'ingredient' => $ingredient->ingredient_name,
                        'needed' => $ingredient->total_needed,
                        'available' => $totalAvailable,
                        'shortage' => $ingredient->total_needed - $totalAvailable
                    ];
                }
            }
        }

        return $insufficientItems;
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
     * Deduct ingredients for an order item
     */
    private function deductIngredients($orderItemId, $quantity)
    {
        // Get all ingredients needed for this order item
        $ingredientsNeeded = DB::select("
            SELECT i.name as ingredient_name, mii.quantity_needed * ? as total_needed
            FROM menu_item_ingredients mii
            INNER JOIN order_items oi ON oi.menu_item_id = mii.menu_item_id
            INNER JOIN ingredients i ON i.id = mii.ingredient_id
            WHERE oi.id = ?
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
     * Deduct a specific packaging item using FIFO method
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

    /**
     * Process cash payment - TAX-INCLUSIVE PRICING
     */
    public function processCashPayment(Request $request)
    {
        // Validate incoming data
        $validated = $request->validate([
            'cash_amount' => 'required|numeric|min:0',
        ]);

        try {
            $cart = Session::get('cart', []);
            $orderType = Session::get('orderType', 'dine-in');
            $cashAmount = floatval($validated['cash_amount']);

            if (empty($cart)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Your cart is empty. Please add items before proceeding.'
                ], 400);
            }

            $subtotal = 0;
            foreach ($cart as $item) {
                $itemPrice = ($item['price'] ?? 0) + ($item['addonsPrice'] ?? 0);
                $subtotal += $itemPrice * ($item['quantity'] ?? 1);
            }

            // For cash payments, prices are treated as tax-inclusive.
            $tax = 0;
            $total = $subtotal;

            if ($cashAmount < $total) {
                return response()->json([
                    'success' => false,
                    'message' => 'Insufficient cash amount provided.'
                ], 400);
            }

            $actualChange = $cashAmount - $total;
            $order = null;


            $stockCheck = $this->checkStockAvailability($cart);
            if (!empty($stockCheck)) {
                $shortageMessage = "Insufficient stock for:\n";
                foreach ($stockCheck as $shortage) {
                    $shortageMessage .= "• {$shortage['menu_item']}: {$shortage['ingredient']} (need {$shortage['needed']}, have {$shortage['available']})\n";
                }

                return response()->json([
                    'success' => false,
                    'message' => 'Sorry, this item is currently unavailable due to ingredient shortage. Please select a different item or contact staff for assistance.'
                ], 400);
            }

            DB::transaction(function () use ($cart, $orderType, $subtotal, $tax, $total, $cashAmount, $actualChange, &$order) {
                // Create the order
                $order = Order::create([
                    'subtotal' => $subtotal,
                    'tax_amount' => $tax,
                    'discount_amount' => 0.00,
                    'total_amount' => $total,
                    'payment_method' => 'cash',
                    'payment_status' => 'pending',
                    'status' => 'pending',
                    'cash_amount' => $cashAmount,
                    'change_amount' => $actualChange,
                    'order_type' => $orderType,
                    'notes' => "Kiosk order - Type: {$orderType}",
                ]);

                // Create order items AND deduct ingredients
                foreach ($cart as $id => $item) {
                    $itemPrice = ($item['price'] ?? 0) + ($item['addonsPrice'] ?? 0);
                    $orderItem = OrderItem::create([
                        'order_id' => $order->id,
                        'menu_item_id' => $item['menu_item_id'] ?? null,
                        'quantity' => $item['quantity'],
                        'unit_price' => $itemPrice,
                        'total_price' => $itemPrice * $item['quantity'],
                        'special_instructions' => isset($item['modifiers']) && is_array($item['modifiers']) ? implode(', ', $item['modifiers']) : null,
                        'status' => 'pending'
                    ]);

                    // Deduct ingredients for each item
                    $this->deductIngredients($orderItem->id, $orderItem->quantity);
                    $this->deductPackagingSupplies($orderItem->id, $orderItem->quantity);
                }
            });

            // Clear the cart and store the last order ID for the confirmation page
            Session::forget('cart');
            if ($order) {
                Session::put('last_order_id', $order->id);
                Log::info('Cash payment processed successfully for Order ID: ' . $order->id);
                $orderNumber = 'C' . str_pad($order->id, 3, '0', STR_PAD_LEFT);
            } else {
                Log::error('Order object is null after transaction.');
                return response()->json([
                    'success' => false,
                    'message' => 'Order could not be created. Please try again.'
                ], 500);
            }

            return response()->json([
                'success' => true,
                'order_id' => $order->id,
                'order_number' => $orderNumber,
                'cash_amount' => $cashAmount,
                'change_amount' => $actualChange,
                'total_amount' => $total,
                'message' => 'Cash order processed successfully. Please proceed to the cashier.'
            ]);
        } catch (Exception $e) {
            Log::error('Cash payment failed: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred. Please contact staff for assistance.'
            ], 500);
        }
    }


    /**
     * Process GCash payment via PayMongo - KEEPS 10% TAX FOR ONLINE PAYMENTS
     */
    public function processGCashPayment(Request $request)
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

            // STEP 1: Save order to database FIRST
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
                    'quantity' => $item['quantity'],
                    'unit_price' => $itemPrice,
                    'total_price' => $itemPrice * $item['quantity'],
                    'special_instructions' => isset($item['addons']) ? json_encode($item['addons']) : null,
                    'status' => 'pending'
                ]);
            }

            // Generate order number
            $orderNumber = 'C' . str_pad($order->id, 3, '0', STR_PAD_LEFT);

            // COMMIT ORDER TO DATABASE - This ensures order exists before PayMongo
            DB::commit();

            Log::info('Order committed to database before PayMongo', [
                'order_id' => $order->id,
                'order_number' => $orderNumber
            ]);

            // STEP 2: NOW CREATE PAYMONGO PAYMENT INTENT (outside transaction)
            $paymongoService = new PaymongoService();

            $paymentIntent = $paymongoService->createPaymentIntent(
                $total,
                'PHP',
                "Order #{$orderNumber} - Sip & Serve Kiosk"
            );

            if (!$paymentIntent || !isset($paymentIntent['data'])) {
                Log::error('PayMongo payment intent creation failed', [
                    'order_id' => $order->id,
                    'total' => $total
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to create payment with PayMongo'
                ]);
            }

            $paymentIntentId = $paymentIntent['data']['id'];

            // STEP 3: Update order with payment intent ID (separate update)
            Order::where('id', $order->id)->update([
                'payment_intent_id' => $paymentIntentId
            ]);

            Log::info('Order updated with payment intent ID via direct query', [
                'order_id' => $order->id,
                'payment_intent_id' => $paymentIntentId
            ]);

            // STEP 4: Create payment method (GCash)
            $paymentMethod = $paymongoService->createPaymentMethod($paymentIntentId, 'gcash');

            if (!$paymentMethod || !isset($paymentMethod['data'])) {
                Log::error('PayMongo payment method creation failed', [
                    'payment_intent_id' => $paymentIntentId
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to create GCash payment method'
                ]);
            }

            $paymentMethodId = $paymentMethod['data']['id'];

            // STEP 5: Attach payment method to payment intent
            $attachResult = $paymongoService->attachPaymentMethod($paymentIntentId, $paymentMethodId);

            if (!$attachResult || !isset($attachResult['data'])) {
                Log::error('PayMongo payment method attachment failed', [
                    'payment_intent_id' => $paymentIntentId,
                    'payment_method_id' => $paymentMethodId
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to attach payment method'
                ]);
            }

            // STEP 6: Update order with payment method ID
            $order->update([
                'payment_method_id' => $paymentMethodId
            ]);

            // Clear cart session
            Session::forget('cart');
            Session::put('last_order_id', $order->id);

            // Get the redirect URL for GCash payment
            $redirectUrl = null;
            if (isset($attachResult['data']['attributes']['next_action']['redirect']['url'])) {
                $redirectUrl = $attachResult['data']['attributes']['next_action']['redirect']['url'];
            }

            Log::info('GCash payment processing completed successfully', [
                'order_id' => $order->id,
                'payment_intent_id' => $paymentIntentId,
                'redirect_url' => $redirectUrl ? 'present' : 'missing'
            ]);

            return response()->json([
                'success' => true,
                'data' => [
                    'payment_intent_id' => $paymentIntentId,
                    'payment_method_id' => $paymentMethodId,
                    'redirect_url' => $redirectUrl,
                    'order_id' => $order->id,
                    'order_number' => $orderNumber,
                    'total_amount' => $total,
                    'status' => $attachResult['data']['attributes']['status']
                ],
                'message' => 'PayMongo GCash payment created successfully'
            ]);
        } catch (Exception $e) {
            Log::error('GCash payment failed: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'GCash payment failed: ' . $e->getMessage()
            ]);
        }
    }



    /**
     * Handle payment success redirect - redirect to order confirmation success
     */


    /**
     * Display the products page
     */
    public function product(): View
    {
        $menu_items = MenuItem::all();
        return view('profile.product', compact('menu_items'));
    }

    /**
     * Handle payment success redirect - redirect to order confirmation success
     */


    /**
     * Handle payment failure redirect
     */

    /**
     * Handle payment success redirect - redirect to order confirmation success
     */
    public function paymentSuccess(Request $request)
    {
        $paymentIntentId = $request->query('payment_intent_id');

        Log::info('Payment success called', [
            'payment_intent_id' => $paymentIntentId,
            'all_params' => $request->all()
        ]);

        if ($paymentIntentId) {
            $order = Order::where('payment_intent_id', $paymentIntentId)->first();

            Log::info('Order lookup result', [
                'order_found' => $order ? true : false,
                'order_id' => $order ? $order->id : null
            ]);

            if ($order) {
                $order->update([
                    'payment_status' => 'paid',        // Changed from 'completed'
                    'status' => 'pending',             // Keep as 'pending' so kitchen sees it
                    'paid_at' => now()
                ]);

                // Now deduct ingredients since payment is confirmed
                foreach ($order->orderItems as $orderItem) {
                    $this->deductIngredients($orderItem->id, $orderItem->quantity);
                }

                // Deduct packaging for takeout orders
                if ($order->order_type === 'take-out') {
                    $this->deductPackagingSupplies($order->orderItems->toArray(), $order->order_type);
                }

                Session::put('last_order_id', $order->id);

                Log::info('Order updated for kitchen processing', [
                    'order_id' => $order->id,
                    'status' => 'pending',
                    'payment_status' => 'paid'
                ]);

                return redirect()->route('kiosk.orderConfirmationSuccess');
            }
        }

        Log::error('Payment success failed - no order found', [
            'payment_intent_id' => $paymentIntentId
        ]);

        return redirect()->route('kiosk.index')->with('error', 'Order not found');
    }

    /**
     * Handle payment failure redirect
     */
    public function paymentFailed(Request $request)
    {
        $paymentIntentId = $request->query('payment_intent_id');

        if ($paymentIntentId) {
            // Find the order by payment_intent_id and update status
            $order = Order::where('payment_intent_id', $paymentIntentId)->first();

            if ($order) {
                // Update order status to failed
                $order->update([
                    'payment_status' => 'failed',
                    'status' => 'cancelled'
                ]);

                // Redirect back to order confirmation with the order ID and error message
                return redirect()->route('kiosk.orderConfirmation', ['id' => $order->id])
                    ->with('payment_error', 'Payment was not completed. Please try again or choose a different payment method.');
            }
        }

        // If no order found, redirect to main kiosk with error
        return redirect()->route('kiosk.index')
            ->with('error', 'Payment failed. Please try again.');
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
        } catch (Exception $e) {
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
        } catch (Exception $e) {
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
        $cart = Session::get('cart', []);
        $orderType = Session::get('orderType', 'dine-in');

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
        } catch (Exception $e) {
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
        } catch (Exception $e) {
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
        } catch (Exception $e) {
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
        } catch (Exception $e) {
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
        } catch (Exception $e) {
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
        } catch (Exception $e) {
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
        } catch (Exception $e) {
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
        } catch (Exception $e) {
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
        } catch (Exception $e) {
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
        try {
            if ($orderId) {
                $order = Order::with(['orderItems.menuItem'])->findOrFail($orderId);
            } else {
                $lastOrderId = Session::get('last_order_id');
                if (!$lastOrderId) {
                    return redirect()->route('kiosk.index')->with('error', 'Order not found');
                }
                $order = Order::with(['orderItems.menuItem'])->findOrFail($lastOrderId);
                Session::forget('last_order_id');
            }

            // Ensure order has required fields for the view
            if (!$order->order_number) {
                $orderNumber = 'C' . str_pad($order->id, 3, '0', STR_PAD_LEFT);
                $order->update(['order_number' => $orderNumber]);
                $order->refresh();
            }

            return view('orderConfirmationSuccess', compact('order'));
        } catch (Exception $e) {
            Log::error('Order confirmation error: ' . $e->getMessage());
            return redirect()->route('kiosk.index')->with('error', 'Order not found');
        }
    }

    public function orderConfirmationSuccess()
    {
        $orderId = session('last_order_id');

        if ($orderId) {
            $order = Order::with('orderItems.menuItem')->find($orderId);

            if ($order) {
                return view('orderConfirmationSuccess', compact('order'));
            }
        }

        // If no order found, redirect to main kiosk
        return redirect()->route('kiosk.index')->with('error', 'Order not found');
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
