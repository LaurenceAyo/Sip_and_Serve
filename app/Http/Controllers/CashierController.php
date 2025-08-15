<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Category;
use App\Models\MenuItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Exception;

class CashierController extends Controller
{
    public function index()
    {
        try {
            Log::info('Cashier index method called');

            // Get pending cash orders only (not preparing ones for the pending panel)
            $cashOrders = Order::with([
                'orderItems',
                'orderItems.menuItem',
                'orderItems.menuItem.category'
            ])
                ->where('payment_method', 'cash')
                ->where('payment_status', 'pending') // Only pending orders for cashier
                ->where('status', 'pending') // Only pending status
                ->orderBy('created_at', 'asc')
                ->get();

            Log::info('Cashier Debug - Orders Found', [
                'count' => $cashOrders->count(),
                'order_ids' => $cashOrders->pluck('id')->toArray(),
                'orders_with_cash_amounts' => $cashOrders->map(function($order) {
                    return [
                        'id' => $order->id,
                        'cash_amount' => $order->cash_amount,
                        'total_amount' => $order->total_amount,
                        'payment_method' => $order->payment_method
                    ];
                })->toArray()
            ]);

            // Get categories for manual order creation
            $categories = Category::with(['menuItems' => function ($query) {
                $query->where('is_available', true);
            }])->where('is_active', true)->get();

            // Format orders for display with FIXED total calculation
            $pendingOrders = $cashOrders->map(function ($order) {
                // Calculate the correct total from order items
                $calculatedTotal = $this->calculateCorrectTotal($order);
                
                // Get cash amount and calculate expected change
                $cashAmount = (float) ($order->cash_amount ?? 0);
                $expectedChange = $cashAmount > 0 ? ($cashAmount - $calculatedTotal) : 0;
                
                return [
                    'id' => $order->id, // Use actual ID, not padded
                    'actual_id' => $order->id,
                    'order_number' => $order->order_number ?? str_pad($order->id, 4, '0', STR_PAD_LEFT),
                    'time' => $order->created_at->format('H:i'),
                    'items' => $this->formatOrderItemsFixed($order->orderItems), // Keep 'items' for view compatibility
                    'order_items' => $this->formatOrderItemsFixed($order->orderItems), // Also provide 'order_items' for JS
                    'total' => $calculatedTotal, // Use calculated total
                    'total_amount' => $calculatedTotal, // Ensure consistency
                    'cash_amount' => $cashAmount,
                    'expected_change' => $expectedChange,
                    'order_type' => $order->order_type ?? 'dine-in',
                    'customer_name' => $order->customer_name,
                    'special_instructions' => $order->special_instructions,
                    'payment_status' => $order->payment_status,
                    'payment_method' => $order->payment_method,
                    'status' => $order->status,
                    'created_at' => $order->created_at->toISOString()
                ];
            })->toArray();

            Log::info('Pending orders formatted with cash amounts', [
                'count' => count($pendingOrders),
                'sample_order' => count($pendingOrders) > 0 ? $pendingOrders[0] : null
            ]);

            return view('cashier', compact('pendingOrders', 'categories'));
        } catch (Exception $e) {
            Log::error('Error loading cashier page', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // Fallback data
            $pendingOrders = [];
            $categories = collect();
            return view('cashier', compact('pendingOrders', 'categories'));
        }
    }

    /**
     * FIXED: Calculate the correct total from order items
     */
    private function calculateCorrectTotal($order)
    {
        $total = 0;
        
        foreach ($order->orderItems as $item) {
            $quantity = (int) $item->quantity;
            $unitPrice = 0;
            
            // Determine unit price with proper fallback logic
            if ($item->unit_price && $item->unit_price > 0) {
                $unitPrice = (float) $item->unit_price;
            } elseif ($item->total_price && $quantity > 0) {
                // Calculate unit price from total price
                $unitPrice = (float) $item->total_price / $quantity;
            } elseif ($item->menuItem && $item->menuItem->price) {
                // Use menu item price as fallback
                $unitPrice = (float) $item->menuItem->price;
            }
            
            $itemTotal = $unitPrice * $quantity;
            $total += $itemTotal;
            
            Log::debug('Item total calculation', [
                'item_id' => $item->id,
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'item_total' => $itemTotal,
                'running_total' => $total
            ]);
        }
        
        // Only use database total if calculated total is 0 (fallback)
        if ($total <= 0 && $order->total_amount > 0) {
            $total = (float) $order->total_amount;
            Log::warning('Using database total as fallback', [
                'order_id' => $order->id,
                'calculated_total' => 0,
                'database_total' => $total
            ]);
        }
        
        Log::info('Final total calculation', [
            'order_id' => $order->id,
            'calculated_total' => $total,
            'database_total' => $order->total_amount
        ]);
        
        return $total;
    }

    /**
     * FIXED: Format order items with proper structure for JavaScript
     */
    private function formatOrderItemsFixed($orderItems)
    {
        return $orderItems->map(function ($item) {
            // Get item name
            $itemName = null;
            if (!empty($item->name)) {
                $itemName = $item->name;
            } elseif ($item->menuItem && !empty($item->menuItem->name)) {
                $itemName = $item->menuItem->name;
            } elseif ($item->menu_item_id) {
                try {
                    $menuItem = MenuItem::find($item->menu_item_id);
                    if ($menuItem) {
                        $itemName = $menuItem->name;
                    }
                } catch (Exception $e) {
                    Log::warning('Could not find menu item', [
                        'menu_item_id' => $item->menu_item_id,
                        'order_item_id' => $item->id
                    ]);
                }
            }

            // Final fallback for name
            if (empty($itemName)) {
                $itemName = 'Menu Item #' . ($item->menu_item_id ?? $item->id);
            }

            // Get quantity
            $quantity = (int) $item->quantity;
            
            // Calculate unit price and total price correctly
            $unitPrice = 0;
            $totalPrice = 0;
            
            if ($item->unit_price && $item->unit_price > 0) {
                $unitPrice = (float) $item->unit_price;
                $totalPrice = $unitPrice * $quantity;
            } elseif ($item->total_price && $item->total_price > 0) {
                $totalPrice = (float) $item->total_price;
                $unitPrice = $quantity > 0 ? $totalPrice / $quantity : 0;
            } elseif ($item->menuItem && $item->menuItem->price) {
                $unitPrice = (float) $item->menuItem->price;
                $totalPrice = $unitPrice * $quantity;
            }

            Log::debug('Formatting order item', [
                'item_id' => $item->id,
                'name' => $itemName,
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'total_price' => $totalPrice
            ]);

            return [
                'name' => $itemName, // Clean name without quantity for JS
                'quantity' => $quantity, // Separate quantity field
                'price' => $totalPrice, // Total price for this item (for view compatibility)
                'total_price' => $totalPrice, // Same as price for consistency
                'unit_price' => $unitPrice, // Unit price
                'item_id' => $item->id,
                'menu_item_id' => $item->menu_item_id
            ];
        })->toArray();
    }

    /**
     * Legacy method - keeping for backward compatibility but redirecting to fixed version
     */
    private function formatOrderItems($orderItems)
    {
        return $this->formatOrderItemsFixed($orderItems);
    }

    public function refreshOrders()
    {
        try {
            Log::info('Refresh orders method called');

            $cashOrders = Order::with(['orderItems', 'orderItems.menuItem'])
                ->where('payment_method', 'cash')
                ->where('payment_status', 'pending')
                ->orderBy('created_at', 'asc')
                ->get();

            Log::info('Refresh orders found', [
                'count' => $cashOrders->count(),
                'orders' => $cashOrders->pluck('id')->toArray(),
                'cash_amounts' => $cashOrders->map(function($order) {
                    return [
                        'id' => $order->id,
                        'cash_amount' => $order->cash_amount,
                        'total_amount' => $order->total_amount
                    ];
                })->toArray()
            ]);

            return response()->json([
                'success' => true,
                'orders' => $cashOrders->map(function ($order) {
                    $calculatedTotal = $this->calculateCorrectTotal($order);
                    $cashAmount = (float) ($order->cash_amount ?? 0);
                    $expectedChange = $cashAmount > 0 ? ($cashAmount - $calculatedTotal) : 0;
                    
                    return [
                        'id' => $order->id,
                        'order_number' => $order->order_number ?? str_pad($order->id, 4, '0', STR_PAD_LEFT),
                        'order_type' => $order->order_type,
                        'total_amount' => $calculatedTotal, // Use calculated total
                        'total' => $calculatedTotal, // Also provide 'total' for compatibility
                        'cash_amount' => $cashAmount,
                        'expected_change' => $expectedChange,
                        'change_amount' => (float) ($order->change_amount ?? 0),
                        'payment_method' => $order->payment_method,
                        'payment_status' => $order->payment_status,
                        'created_at' => $order->created_at->toISOString(),
                        'order_items' => $this->formatOrderItemsFixed($order->orderItems)
                    ];
                })
            ], 200, [
                'Content-Type' => 'application/json'
            ]);
        } catch (Exception $e) {
            Log::error('Error refreshing cashier orders', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to refresh orders: ' . $e->getMessage()
            ], 500, [
                'Content-Type' => 'application/json'
            ]);
        }
    }

    public function acceptOrder(Request $request)
    {
        Log::info('Accept order method called', [
            'request_data' => $request->all(),
            'headers' => $request->headers->all()
        ]);

        try {
            $validated = $request->validate([
                'order_id' => 'required|integer|exists:orders,id',
                'cash_amount' => 'required|numeric|min:0',
                'print_receipt' => 'boolean'
            ]);

            Log::info('Validation passed', ['validated' => $validated]);
        } catch (ValidationException $e) {
            Log::error('Validation failed', [
                'errors' => $e->errors(),
                'input' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        }

        DB::beginTransaction();

        try {
            $order = Order::with(['orderItems.menuItem'])->findOrFail($validated['order_id']);

            // Use calculated total instead of database total
            $calculatedTotal = $this->calculateCorrectTotal($order);

            Log::info('Order found with calculated total', [
                'order_id' => $order->id,
                'database_total' => $order->total_amount,
                'calculated_total' => $calculatedTotal,
                'cash_amount' => $validated['cash_amount']
            ]);

            // Validate cash amount against calculated total
            if ($validated['cash_amount'] < $calculatedTotal) {
                Log::warning('Insufficient cash amount', [
                    'cash_amount' => $validated['cash_amount'],
                    'calculated_total' => $calculatedTotal
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Cash amount is insufficient'
                ], 422);
            }

            // Calculate change using calculated total
            $changeAmount = $validated['cash_amount'] - $calculatedTotal;

            Log::info('Updating order with calculated values', [
                'order_id' => $order->id,
                'calculated_total' => $calculatedTotal,
                'change_amount' => $changeAmount
            ]);

            // Update order with calculated total
            $order->update([
                'cash_amount' => $validated['cash_amount'],
                'change_amount' => $changeAmount,
                'total_amount' => $calculatedTotal, // Update with calculated total
                'payment_status' => 'paid',
                'status' => 'preparing',
                'paid_at' => now(),
                'kitchen_received_at' => now(),
            ]);

            $receiptPrinted = false;

            // Print receipt if requested
            if ($validated['print_receipt'] ?? true) {
                $receiptPrinted = $this->printReceipt($order);
            }

            DB::commit();

            Log::info('Order accepted by cashier', [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'calculated_total' => $calculatedTotal,
                'cash_amount' => $validated['cash_amount'],
                'change_amount' => $changeAmount,
                'receipt_printed' => $receiptPrinted
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Order accepted successfully',
                'receipt_printed' => $receiptPrinted,
                'change_amount' => $changeAmount,
                'total_amount' => $calculatedTotal, // Return calculated total
                'order' => [
                    'id' => $order->id,
                    'status' => $order->status,
                    'payment_status' => $order->payment_status,
                    'total_amount' => $calculatedTotal
                ]
            ], 200, [
                'Content-Type' => 'application/json'
            ]);
        } catch (Exception $e) {
            DB::rollback();

            Log::error('Error accepting order', [
                'order_id' => $validated['order_id'] ?? 'unknown',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to accept order: ' . $e->getMessage()
            ], 500, [
                'Content-Type' => 'application/json'
            ]);
        }
    }

    public function completeOrder(Request $request)
    {
        $validated = $request->validate([
            'order_id' => 'required|integer|exists:orders,id',
            'order_number' => 'required|string',
            'completion_time' => 'required|string',
            'change_given' => 'required|numeric',
            'receipt_printed' => 'required|boolean'
        ]);

        try {
            // Update order status to completed
            $order = Order::findOrFail($validated['order_id']);
            $order->update([
                'status' => 'completed',
                'completed_at' => $validated['completion_time']
            ]);

            // Insert into daily_sales table
            DB::table('daily_sales')->insert([
                'order_id' => $validated['order_id'],
                'order_number' => $validated['order_number'],
                'total_amount' => $order->total_amount,
                'cash_received' => $order->cash_amount,
                'change_given' => $validated['change_given'],
                'completion_time' => $validated['completion_time'],
                'receipt_printed' => $validated['receipt_printed'],
                'created_at' => now(),
                'updated_at' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Order completed and saved to sales'
            ]);
        } catch (Exception $e) {
            Log::error('Error completing order', [
                'order_id' => $validated['order_id'],
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to complete order'
            ], 500);
        }
    }

    public function cancelOrder(Request $request)
    {
        Log::info('Cancel order method called', [
            'request_data' => $request->all()
        ]);

        try {
            $validated = $request->validate([
                'order_id' => 'required|integer|exists:orders,id',
                'reason' => 'nullable|string|max:255'
            ]);
        } catch (ValidationException $e) {
            Log::error('Cancel order validation failed', [
                'errors' => $e->errors(),
                'input' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        }

        DB::beginTransaction();

        try {
            $order = Order::findOrFail($validated['order_id']);

            Log::info('Cancelling order', [
                'order_id' => $order->id,
                'reason' => $validated['reason'] ?? 'No reason provided'
            ]);

            // Update order status to cancelled
            $order->update([
                'status' => 'cancelled',
                'payment_status' => 'cancelled',
                'cancelled_at' => now(),
                'cancellation_reason' => $validated['reason'] ?? 'Cancelled by cashier'
            ]);

            DB::commit();

            Log::info('Order cancelled by cashier', [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'reason' => $validated['reason']
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Order cancelled successfully'
            ], 200, [
                'Content-Type' => 'application/json'
            ]);
        } catch (Exception $e) {
            DB::rollback();

            Log::error('Error cancelling order', [
                'order_id' => $validated['order_id'] ?? 'unknown',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to cancel order: ' . $e->getMessage()
            ], 500, [
                'Content-Type' => 'application/json'
            ]);
        }
    }

    private function printReceipt(Order $order)
    {
        try {
            // For production environments with actual thermal printers
            // Uncomment and configure your printer connection
            // $connector = new WindowsPrintConnector("POS-80");

            // For development - save receipt to file and simulate printing
            $receiptPath = storage_path('app/receipts');
            if (!file_exists($receiptPath)) {
                mkdir($receiptPath, 0755, true);
            }

            $receiptFile = $receiptPath . '/receipt_' . $order->id . '_' . now()->format('YmdHis') . '.txt';

            // Generate receipt content
            $receiptContent = $this->generateReceiptContent($order);

            // Save receipt to file
            file_put_contents($receiptFile, $receiptContent);

            // Log successful receipt generation
            Log::info('Receipt generated successfully', [
                'order_id' => $order->id,
                'file_path' => $receiptFile,
                'order_total' => $order->total_amount,
                'cash_amount' => $order->cash_amount,
                'change_amount' => $order->change_amount
            ]);

            // Simulate cash register opening (in production, this would trigger hardware)
            $this->openCashDrawer();

            return true;
        } catch (Exception $e) {
            Log::error('Receipt printing failed', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return false;
        }
    }

    private function generateReceiptContent(Order $order)
    {
        $receipt = "";
        $receipt .= "=====================================\n";
        $receipt .= "          SIP & SERVE CAFE          \n";
        $receipt .= "=====================================\n";
        $receipt .= "         OFFICIAL RECEIPT           \n";
        $receipt .= "=====================================\n\n";

        // Order details
        $receipt .= "Receipt #: " . ($order->order_number ?? str_pad($order->id, 4, '0', STR_PAD_LEFT)) . "\n";
        $receipt .= "Date: " . $order->created_at->format('M d, Y H:i') . "\n";
        $receipt .= "Cashier: " . (Auth::user()->name ?? 'System') . "\n";
        $receipt .= "Type: " . ucfirst($order->order_type ?? 'Dine-in') . "\n";

        if ($order->customer_name) {
            $receipt .= "Customer: " . $order->customer_name . "\n";
        }

        $receipt .= "\n-------------------------------------\n";
        $receipt .= "ITEMS:\n";
        $receipt .= "-------------------------------------\n";

        // Order items with correct calculations
        $calculatedSubtotal = 0;
        foreach ($order->orderItems as $item) {
            $itemName = $item->name ?? $item->menuItem->name ?? 'Custom Item';
            $quantity = (int) $item->quantity;
            
            // Calculate unit price correctly
            $unitPrice = 0;
            if ($item->unit_price && $item->unit_price > 0) {
                $unitPrice = (float) $item->unit_price;
            } elseif ($item->total_price && $quantity > 0) {
                $unitPrice = (float) $item->total_price / $quantity;
            } elseif ($item->menuItem && $item->menuItem->price) {
                $unitPrice = (float) $item->menuItem->price;
            }
            
            $totalPrice = $unitPrice * $quantity;
            $calculatedSubtotal += $totalPrice;

            $receipt .= $itemName . "\n";
            $receipt .= "  " . $quantity . " x PHP " . number_format($unitPrice, 2);
            $receipt .= " = PHP " . number_format($totalPrice, 2) . "\n";
        }

        $receipt .= "\n-------------------------------------\n";

        // Use calculated totals
        $taxAmount = is_numeric($order->tax_amount) ? (float) $order->tax_amount : 0;
        $discountAmount = is_numeric($order->discount_amount) ? (float) $order->discount_amount : 0;
        $totalAmount = $calculatedSubtotal + $taxAmount - $discountAmount;
        $cashAmount = is_numeric($order->cash_amount) ? (float) $order->cash_amount : 0;
        $changeAmount = is_numeric($order->change_amount) ? (float) $order->change_amount : 0;

        $receipt .= "Subtotal: PHP " . number_format($calculatedSubtotal, 2) . "\n";

        if ($taxAmount > 0) {
            $receipt .= "VAT (12%): PHP " . number_format($taxAmount, 2) . "\n";
        }

        if ($discountAmount > 0) {
            $receipt .= "Discount: -PHP " . number_format($discountAmount, 2) . "\n";
        }

        $receipt .= "\n=====================================\n";
        $receipt .= "TOTAL: PHP " . number_format($totalAmount, 2) . "\n";
        $receipt .= "=====================================\n\n";

        // Payment details
        $receipt .= "PAYMENT DETAILS:\n";
        $receipt .= "-------------------------------------\n";
        $receipt .= "Payment Method: CASH\n";
        $receipt .= "Cash Received: PHP " . number_format($cashAmount, 2) . "\n";

        if ($changeAmount > 0) {
            $receipt .= "Change: PHP " . number_format($changeAmount, 2) . "\n";
        }

        $receipt .= "Status: PAID\n";
        $receipt .= "\n=====================================\n";

        // Special instructions
        if ($order->special_instructions) {
            $receipt .= "Special Instructions:\n";
            $receipt .= $order->special_instructions . "\n";
            $receipt .= "-------------------------------------\n";
        }

        // Footer
        $receipt .= "\n       Thank you for dining\n";
        $receipt .= "            with us!\n";
        $receipt .= "        Please come again!\n\n";
        $receipt .= "=====================================\n";
        $receipt .= "BIR Permit #: 12345678\n";
        $receipt .= "TIN: 123-456-789-000\n";
        $receipt .= "www.sipandserve.com\n";
        $receipt .= "=====================================\n";

        return $receipt;
    }

    private function openCashDrawer()
    {
        try {
            // In production, this would send commands to open the cash drawer
            // For development, we'll just log the action
            Log::info('Cash drawer opened', [
                'timestamp' => now(),
                'action' => 'drawer_open',
                'cashier' => Auth::user()->name ?? 'System'
            ]);

            // If you have a physical cash drawer, uncomment and configure:
            // $this->sendCashDrawerCommand();

        } catch (Exception $e) {
            Log::error('Failed to open cash drawer', [
                'error' => $e->getMessage()
            ]);
        }
    }

    // Uncomment and implement for physical cash drawer integration
    /*
    private function sendCashDrawerCommand()
    {
        // ESC/POS command to open cash drawer (Drawer 1)
        $drawerCommand = "\x1B\x70\x00\x19\x19";
        
        // Send to printer (which typically controls the cash drawer)
        $connector = new WindowsPrintConnector("POS-80");
        $printer = new Printer($connector);
        $printer->text($drawerCommand);
        $printer->close();
    }
    */

    public function getDashboardStats()
    {
        try {
            $today = now()->startOfDay();

            $stats = [
                'today_orders' => Order::whereDate('created_at', $today)->count(),
                'today_revenue' => (float) Order::whereDate('created_at', $today)
                    ->where('payment_status', 'paid')
                    ->sum('total_amount'),
                'pending_orders' => Order::where('payment_status', 'pending')->count(),
                'preparing_orders' => Order::where('status', 'preparing')->count()
            ];

            return response()->json([
                'success' => true,
                'stats' => $stats
            ], 200, [
                'Content-Type' => 'application/json'
            ]);
        } catch (Exception $e) {
            Log::error('Error getting dashboard stats', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to get stats'
            ], 500);
        }
    }

    public function createManualOrder(Request $request)
    {
        try {
            $validated = $request->validate([
                'items' => 'required|array|min:1',
                'items.*.menu_item_id' => 'required|integer|exists:menu_items,id',
                'items.*.quantity' => 'required|integer|min:1',
                'order_type' => 'required|in:dine-in,take-out',
                'customer_name' => 'nullable|string|max:255',
                'special_instructions' => 'nullable|string|max:500'
            ]);

            DB::beginTransaction();

            // Create order
            $order = Order::create([
                'order_number' => 'MAN-' . now()->format('YmdHis'),
                'order_type' => $validated['order_type'],
                'customer_name' => $validated['customer_name'],
                'special_instructions' => $validated['special_instructions'],
                'payment_method' => 'cash',
                'payment_status' => 'pending',
                'status' => 'pending',
                'subtotal' => 0,
                'tax_amount' => 0,
                'total_amount' => 0,
                'created_by' => Auth::id()
            ]);

            $totalAmount = 0;

            // Add order items
            foreach ($validated['items'] as $itemData) {
                $menuItem = MenuItem::findOrFail($itemData['menu_item_id']);
                $quantity = $itemData['quantity'];
                $unitPrice = $menuItem->price;
                $itemTotal = $unitPrice * $quantity;

                OrderItem::create([
                    'order_id' => $order->id,
                    'menu_item_id' => $menuItem->id,
                    'name' => $menuItem->name,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'total_price' => $itemTotal
                ]);

                $totalAmount += $itemTotal;
            }

            // Update order totals
            $order->update([
                'subtotal' => $totalAmount,
                'total_amount' => $totalAmount
            ]);

            DB::commit();

            Log::info('Manual order created', [
                'order_id' => $order->id,
                'created_by' => Auth::id(),
                'total_amount' => $totalAmount
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Manual order created successfully',
                'order' => [
                    'id' => $order->id,
                    'order_number' => $order->order_number,
                    'total_amount' => $order->total_amount
                ]
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (Exception $e) {
            DB::rollback();

            Log::error('Error creating manual order', [
                'error' => $e->getMessage(),
                'input' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to create manual order: ' . $e->getMessage()
            ], 500);
        }
    }
}