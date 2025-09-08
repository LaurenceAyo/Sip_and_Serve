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
use App\Services\ThermalPrinterService;
use Exception;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\Printer;

class CashierController extends Controller
{
    protected $thermalPrinterService;

    public function __construct()
    {
        $this->thermalPrinterService = new ThermalPrinterService();
    }

    public function index()
    {
        try {
            Log::info('Cashier index method called - GOOJPRT PT-210 System');

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

            Log::info('GOOJPRT PT-210 Cashier Debug - Orders Found', [
                'count' => $cashOrders->count(),
                'order_ids' => $cashOrders->pluck('id')->toArray(),
                'printer_configured' => env('THERMAL_PRINTER_NAME', 'Not configured'),
                'printer_path' => env('THERMAL_PRINTER_PATH', 'Not configured')
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

            Log::info('GOOJPRT PT-210 system - Pending orders formatted', [
                'count' => count($pendingOrders),
                'sample_order' => count($pendingOrders) > 0 ? $pendingOrders[0] : null
            ]);

            return view('cashier', compact('pendingOrders', 'categories'));
        } catch (Exception $e) {
            Log::error('Error loading GOOJPRT PT-210 cashier page', [
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

            Log::debug('GOOJPRT PT-210 - Item total calculation', [
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
            Log::warning('GOOJPRT PT-210 - Using database total as fallback', [
                'order_id' => $order->id,
                'calculated_total' => 0,
                'database_total' => $total
            ]);
        }

        Log::info('GOOJPRT PT-210 - Final total calculation', [
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
                    Log::warning('GOOJPRT PT-210 - Could not find menu item', [
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

            Log::debug('GOOJPRT PT-210 - Formatting order item', [
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

    public function refreshOrders()
    {
        try {
            Log::info('GOOJPRT PT-210 - Refresh orders method called');

            // Fix: Use 'status' not 'payment_status' for pending orders
            $cashOrders = Order::with(['orderItems', 'orderItems.menuItem'])
                ->where('payment_method', 'cash')
                ->where('status', 'pending')  // Changed from payment_status
                ->orderBy('created_at', 'asc')
                ->get();

            Log::info('GOOJPRT PT-210 - Refresh orders found', [
                'count' => $cashOrders->count(),
                'orders' => $cashOrders->pluck('id')->toArray()
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
                        'total_amount' => $calculatedTotal,
                        'total' => $calculatedTotal,
                        'cash_amount' => $cashAmount,
                        'expected_change' => $expectedChange,
                        'change_amount' => (float) ($order->change_amount ?? 0),
                        'payment_method' => $order->payment_method,
                        'payment_status' => 'pending', // Add this for JS compatibility
                        'status' => $order->status,
                        'created_at' => $order->created_at->toISOString(),
                        'order_items' => $this->formatOrderItemsFixed($order->orderItems)
                    ];
                })
            ]);
        } catch (Exception $e) {
            Log::error('GOOJPRT PT-210 - Error refreshing cashier orders', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to refresh orders: ' . $e->getMessage()
            ], 500);
        }
    }

    public function acceptOrder(Request $request)
    {
        Log::info('GOOJPRT PT-210 - Accept order method called', [
            'request_data' => $request->all(),
            'printer_configured' => env('THERMAL_PRINTER_NAME', 'Not configured')
        ]);

        try {
            $validated = $request->validate([
                'order_id' => 'required|integer|exists:orders,id',
                'cash_amount' => 'required|numeric|min:0',
                'print_receipt' => 'boolean'
            ]);

            Log::info('GOOJPRT PT-210 - Validation passed', ['validated' => $validated]);
        } catch (ValidationException $e) {
            Log::error('GOOJPRT PT-210 - Validation failed', [
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

            Log::info('GOOJPRT PT-210 - Order found with calculated total', [
                'order_id' => $order->id,
                'database_total' => $order->total_amount,
                'calculated_total' => $calculatedTotal,
                'cash_amount' => $validated['cash_amount']
            ]);

            // Validate cash amount against calculated total
            if ($validated['cash_amount'] < $calculatedTotal) {
                Log::warning('GOOJPRT PT-210 - Insufficient cash amount', [
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

            Log::info('GOOJPRT PT-210 - Updating order with calculated values', [
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
            $printerError = null;

            // Print receipt using GOOJPRT PT-210 if requested
            if ($validated['print_receipt'] ?? true) {
                Log::info('GOOJPRT PT-210 - Attempting to print receipt', [
                    'order_id' => $order->id,
                    'printer_name' => env('THERMAL_PRINTER_NAME'),
                    'printer_path' => env('THERMAL_PRINTER_PATH')
                ]);

                try {
                    $receiptPrinted = $this->thermalPrinterService->printReceipt($order);

                    Log::info('GOOJPRT PT-210 - Receipt printing result', [
                        'order_id' => $order->id,
                        'receipt_printed' => $receiptPrinted
                    ]);
                } catch (Exception $e) {
                    $printerError = $e->getMessage();
                    Log::error('GOOJPRT PT-210 - Receipt printing exception', [
                        'order_id' => $order->id,
                        'error' => $printerError
                    ]);
                    $receiptPrinted = false;
                }
            }

            DB::commit();

            Log::info('GOOJPRT PT-210 - Order accepted by cashier', [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'calculated_total' => $calculatedTotal,
                'cash_amount' => $validated['cash_amount'],
                'change_amount' => $changeAmount,
                'receipt_printed' => $receiptPrinted,
                'printer_error' => $printerError
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Order accepted successfully',
                'receipt_printed' => $receiptPrinted,
                'change_amount' => $changeAmount,
                'total_amount' => $calculatedTotal, // Return calculated total
                'printer_info' => 'GOOJPRT PT-210 USB',
                'printer_error' => $printerError,
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

            Log::error('GOOJPRT PT-210 - Error accepting order', [
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
                'printer_used' => 'GOOJPRT PT-210',
                'created_at' => now(),
                'updated_at' => now()
            ]);

            Log::info('GOOJPRT PT-210 - Order completed and saved to sales', [
                'order_id' => $validated['order_id']
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Order completed and saved to sales'
            ]);
        } catch (Exception $e) {
            Log::error('GOOJPRT PT-210 - Error completing order', [
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
        Log::info('GOOJPRT PT-210 - Cancel order method called', [
            'request_data' => $request->all()
        ]);

        try {
            $validated = $request->validate([
                'order_id' => 'required|integer|exists:orders,id',
                'reason' => 'nullable|string|max:255'
            ]);
        } catch (ValidationException $e) {
            Log::error('GOOJPRT PT-210 - Cancel order validation failed', [
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

            Log::info('GOOJPRT PT-210 - Cancelling order', [
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

            Log::info('GOOJPRT PT-210 - Order cancelled by cashier', [
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

            Log::error('GOOJPRT PT-210 - Error cancelling order', [
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

    // ENHANCED: Printer testing routes with proper GOOJPRT PT-210 integration
    public function testPrinterPrint()
    {
        try {
            Log::info('GOOJPRT PT-210 - Manual printer test requested via controller');

            $result = $this->thermalPrinterService->testPrinter();
            $connectionInfo = $this->thermalPrinterService->getConnectionInfo();

            return response()->json([
                'success' => $result,
                'message' => $result ? 'GOOJPRT PT-210 test print sent successfully!' : 'GOOJPRT PT-210 test print failed',
                'connection_info' => $connectionInfo,
                'timestamp' => now()->toISOString()
            ]);
        } catch (Exception $e) {
            Log::error('GOOJPRT PT-210 - Printer test controller error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'message' => 'GOOJPRT PT-210 test failed: ' . $e->getMessage()
            ], 500);
        }
    }

    public function testPrinterConnection()
    {
        try {
            Log::info('GOOJPRT PT-210 - Printer connection info requested via controller');

            $connectionInfo = $this->thermalPrinterService->getConnectionInfo();

            return response()->json([
                'success' => true,
                'connection_info' => $connectionInfo,
                'printer_model' => 'GOOJPRT PT-210',
                'connection_status' => 'Ready to test'
            ]);
        } catch (Exception $e) {
            Log::error('GOOJPRT PT-210 - Printer connection info controller error', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'connection_info' => [
                    'configured_printer_name' => env('THERMAL_PRINTER_NAME', 'Not configured'),
                    'configured_printer_path' => env('THERMAL_PRINTER_PATH', 'Not configured'),
                    'error' => 'Could not retrieve printer information'
                ]
            ], 500);
        }
    }

    public function resetPrinterConnection()
    {
        try {
            Log::info('GOOJPRT PT-210 - Manual printer connection reset requested');

            // Clear any cached connections or temporary files
            $receiptPath = storage_path('app/thermal_receipts');
            if (file_exists($receiptPath)) {
                $files = glob($receiptPath . '/*');
                foreach ($files as $file) {
                    if (is_file($file) && (time() - filemtime($file)) > 3600) { // Delete files older than 1 hour
                        unlink($file);
                    }
                }
            }

            // Re-initialize printer service
            $this->thermalPrinterService = new ThermalPrinterService();
            $testResult = $this->thermalPrinterService->testPrinter();

            return response()->json([
                'success' => true,
                'message' => 'GOOJPRT PT-210 connection reset completed',
                'test_result' => $testResult,
                'timestamp' => now()->toISOString()
            ]);
        } catch (Exception $e) {
            Log::error('GOOJPRT PT-210 - Printer reset error', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'message' => 'Failed to reset GOOJPRT PT-210 connection'
            ], 500);
        }
    }

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
                'preparing_orders' => Order::where('status', 'preparing')->count(),
                'printer_status' => 'GOOJPRT PT-210 Ready',
                'printer_name' => env('THERMAL_PRINTER_NAME', 'GOOJPRT PT-210'),
                'printer_path' => env('THERMAL_PRINTER_PATH', 'GOOJPRT PT-210')
            ];

            return response()->json([
                'success' => true,
                'stats' => $stats
            ], 200, [
                'Content-Type' => 'application/json'
            ]);
        } catch (Exception $e) {
            Log::error('GOOJPRT PT-210 - Error getting dashboard stats', [
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

            Log::info('GOOJPRT PT-210 - Manual order created', [
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

            Log::error('GOOJPRT PT-210 - Error creating manual order', [
                'error' => $e->getMessage(),
                'input' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to create manual order: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update order items (for edit functionality)
     */
    public function updateOrder(Request $request)
    {
        try {
            $validated = $request->validate([
                'order_id' => 'required|integer|exists:orders,id',
                'items' => 'required|array|min:1',
                'items.*.name' => 'required|string',
                'items.*.price' => 'required|numeric|min:0',
                'items.*.quantity' => 'required|integer|min:1',
                'items.*.total_price' => 'required|numeric|min:0'
            ]);

            DB::beginTransaction();

            $order = Order::findOrFail($validated['order_id']);

            // Check if order is still pending
            if ($order->payment_status !== 'pending') {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot edit order - payment already processed'
                ], 422);
            }

            // Delete existing order items
            $order->orderItems()->delete();

            $newTotal = 0;

            // Add updated order items
            foreach ($validated['items'] as $itemData) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'name' => $itemData['name'],
                    'quantity' => $itemData['quantity'],
                    'unit_price' => $itemData['price'],
                    'total_price' => $itemData['total_price']
                ]);

                $newTotal += $itemData['total_price'];
            }

            // Update order total
            $order->update([
                'total_amount' => $newTotal,
                'subtotal' => $newTotal
            ]);

            DB::commit();

            Log::info('GOOJPRT PT-210 - Order updated', [
                'order_id' => $order->id,
                'new_total' => $newTotal,
                'items_count' => count($validated['items'])
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Order updated successfully',
                'new_total' => $newTotal
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (Exception $e) {
            DB::rollback();

            Log::error('GOOJPRT PT-210 - Error updating order', [
                'error' => $e->getMessage(),
                'input' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to update order: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get detailed printer diagnostics
     */
    public function printerDiagnostics()
    {
        try {
            $diagnostics = [
                'timestamp' => now()->toISOString(),
                'printer_model' => 'GOOJPRT PT-210',
                'environment_config' => [
                    'THERMAL_PRINTER_NAME' => env('THERMAL_PRINTER_NAME'),
                    'THERMAL_PRINTER_TYPE' => env('THERMAL_PRINTER_TYPE'),
                    'THERMAL_PRINTER_PATH' => env('THERMAL_PRINTER_PATH'),
                    'THERMAL_PRINTER_DEBUG' => env('THERMAL_PRINTER_DEBUG'),
                ],
                'system_info' => [
                    'php_version' => PHP_VERSION,
                    'os_family' => PHP_OS_FAMILY,
                    'os' => php_uname(),
                ],
                'printer_library' => [
                    'escpos_installed' => class_exists('Mike42\Escpos\Printer'),
                    'version_info' => 'mike42/escpos-php library loaded'
                ]
            ];

            // Test basic printer connectivity
            try {
                $connectionInfo = $this->thermalPrinterService->getConnectionInfo();
                $diagnostics['connection_test'] = $connectionInfo;
                $diagnostics['connection_status'] = 'Available';
            } catch (Exception $e) {
                $diagnostics['connection_test'] = ['error' => $e->getMessage()];
                $diagnostics['connection_status'] = 'Failed';
            }

            // Get Windows printer list if possible
            if (PHP_OS_FAMILY === 'Windows') {
                try {
                    $output = shell_exec('wmic printer get name,portname,drivername /format:csv 2>nul');
                    $diagnostics['windows_printers'] = $output ? explode("\n", $output) : ['Could not retrieve'];
                } catch (Exception $e) {
                    $diagnostics['windows_printers'] = ['Error: ' . $e->getMessage()];
                }
            }

            return response()->json([
                'success' => true,
                'diagnostics' => $diagnostics
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'diagnostics' => [
                    'timestamp' => now()->toISOString(),
                    'error' => 'Could not complete diagnostics'
                ]
            ], 500);
        }
    }
}
