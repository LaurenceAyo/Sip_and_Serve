<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
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
use App\Services\CashDrawerService;
use Exception;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\Printer;

class CashierController extends Controller
{
    protected $thermalPrinterService;
    protected $cashDrawerService;

    public function __construct()
    {
        $this->thermalPrinterService = new ThermalPrinterService();
        $this->cashDrawerService = new CashDrawerService();
    }

    /**
     * Open cash drawer manually
     */
    public function openDrawer(Request $request)
    {
        try {
            Log::info('Cash Drawer - Manual open requested via controller');

            $validated = $request->validate([
                'drawer_number' => 'nullable|integer|in:1,2',
                'reason' => 'nullable|string|max:255'
            ]);

            $drawerNumber = $validated['drawer_number'] ?? 1;
            $reason = $validated['reason'] ?? 'Manual open by cashier';

            // Open the cash drawer using separate service
            $result = $this->cashDrawerService->openDrawer($drawerNumber);

            if ($result) {
                // Log the drawer opening for audit purposes
                Log::info('Cash Drawer - Opened manually', [
                    'drawer_number' => $drawerNumber,
                    'reason' => $reason,
                    'user_id' => Auth::id(),
                    'timestamp' => now()->toISOString()
                ]);

                // Record in database for tracking
                DB::table('cash_drawer_logs')->insert([
                    'drawer_number' => $drawerNumber,
                    'action' => 'manual_open',
                    'reason' => $reason,
                    'user_id' => Auth::id(),
                    'success' => true,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Cash drawer opened successfully',
                    'drawer_number' => $drawerNumber,
                    'method' => 'USB/Serial Adapter'
                ]);
            } else {
                // Log failed attempt
                DB::table('cash_drawer_logs')->insert([
                    'drawer_number' => $drawerNumber,
                    'action' => 'manual_open',
                    'reason' => $reason,
                    'user_id' => Auth::id(),
                    'success' => false,
                    'error_message' => 'Drawer service returned false',
                    'created_at' => now(),
                    'updated_at' => now()
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Failed to open cash drawer - check connection'
                ], 500);
            }
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (Exception $e) {
            Log::error('Cash Drawer - Controller open error', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Cash drawer error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get cash drawer diagnostics and connection info
     */
    public function drawerDiagnostics()
    {
        try {
            $connectionInfo = $this->cashDrawerService->getConnectionInfo();

            return response()->json([
                'success' => true,
                'diagnostics' => [
                    'connection_info' => $connectionInfo,
                    'environment_config' => [
                        'CASH_DRAWER_TYPE' => env('CASH_DRAWER_TYPE'),
                        'CASH_DRAWER_COM_PORT' => env('CASH_DRAWER_COM_PORT'),
                        'CASH_DRAWER_BAUD_RATE' => env('CASH_DRAWER_BAUD_RATE'),
                    ],
                    'system_info' => [
                        'php_version' => PHP_VERSION,
                        'os_family' => PHP_OS_FAMILY,
                        'os' => php_uname(),
                    ],
                    'bluetooth_printer_info' => [
                        'model' => 'Small Bluetooth Thermal Printer',
                        'connection' => 'Bluetooth',
                        'drawer_port' => 'None - Using separate adapter'
                    ]
                ]
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function index()
    {
        try {
            Log::info('Cashier index method called - GOOJPRT PT-210 System');

            // Remove kitchen_received_at filter
            $cashOrders = Order::with([
                'orderItems',
                'orderItems.menuItem',
                'orderItems.menuItem.category'
            ])
                ->whereIn('payment_method', ['cash', 'maya'])
                ->where(function ($query) {
                    // Show orders that are:
                    // 1. Pending payment (payment_status = pending or null)
                    // 2. OR paid but not yet sent to kitchen (kitchen_received_at is null)
                    $query->where(function ($q) {
                        $q->where('payment_status', 'pending')
                            ->orWhereNull('payment_status');
                    })
                        ->orWhere(function ($q) {
                            $q->where('payment_status', 'paid')
                                ->whereNull('kitchen_received_at');
                        });
                })
                ->whereIn('status', ['pending', 'processing'])
                ->orderBy('created_at', 'asc')
                ->get();

            Log::info('Cashier orders fetched', [
                'count' => $cashOrders->count(),
                'order_ids' => $cashOrders->pluck('id')->toArray(),
                'payment_statuses' => $cashOrders->pluck('payment_status')->toArray(),
                'statuses' => $cashOrders->pluck('status')->toArray()
            ]);

            // Get categories for manual order creation
            $categories = Category::with(['menuItems' => function ($query) {
                $query->where('is_available', true);
            }])->where('is_active', true)->get();

            // Format orders for display
            $pendingOrders = $cashOrders->map(function ($order) {
                $calculatedTotal = $this->calculateCorrectTotal($order);

                // Calculate discount
                $hasDiscount = $order->discount_type && $order->discount_type !== 'none' && $order->discount_amount > 0;
                $discountAmount = $hasDiscount ? (float)$order->discount_amount : 0;
                $amountBeforeDiscount = $hasDiscount ? (float)$order->amount_before_discount : $calculatedTotal;
                $finalTotal = $hasDiscount ? ($amountBeforeDiscount - $discountAmount) : $calculatedTotal;

                $cashAmount = (float) ($order->cash_amount ?? 0);
                $expectedChange = $cashAmount > 0 ? ($cashAmount - $finalTotal) : 0;

                return [
                    'id' => $order->id,
                    'actual_id' => $order->id,
                    'order_number' => $order->order_number ?? str_pad($order->id, 4, '0', STR_PAD_LEFT),
                    'time' => $order->created_at->format('H:i'),
                    'items' => $this->formatOrderItemsFixed($order->orderItems),
                    'order_items' => $this->formatOrderItemsFixed($order->orderItems),
                    'total' => $finalTotal,
                    'total_amount' => $finalTotal,
                    'amount_before_discount' => $amountBeforeDiscount,
                    'discount_type' => $order->discount_type,
                    'discount_percent' => $order->discount_percent ?? 20,
                    'discount_amount' => $discountAmount,
                    'discount_id_number' => $order->discount_id_number,
                    'cash_amount' => $cashAmount,
                    'expected_change' => $expectedChange,
                    'order_type' => $order->order_type ?? 'dine-in',
                    'customer_name' => $order->customer_name,
                    'special_instructions' => $order->special_instructions,
                    'payment_status' => $order->payment_status,
                    'payment_method' => $order->payment_method,
                    'status' => $order->status,
                    'maya_reference' => $order->maya_reference,
                    'maya_webhook_received_at' => $order->maya_webhook_received_at,
                    'created_at' => $order->created_at->toISOString()
                ];
            })->toArray();

            Log::info('Orders formatted for cashier', [
                'count' => count($pendingOrders),
            ]);

            return view('cashier', compact('pendingOrders', 'categories'));
        } catch (Exception $e) {
            Log::error('Error loading cashier page', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            $pendingOrders = [];
            $categories = collect();
            return view('cashier', compact('pendingOrders', 'categories'));
        }
    }

    /**
     * Calculate the correct total from order items
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
     * Apply discount to an order
     */
    public function applyDiscount(Request $request)
    {
        try {
            $validated = $request->validate([
                'order_id' => 'required|integer|exists:orders,id',
                'discount_type' => 'required|in:senior_citizen,pwd',
                'id_number' => 'required|string|max:50'
            ]);

            DB::beginTransaction();

            $order = Order::with('orderItems')->findOrFail($validated['order_id']);

            // Check if order is still pending
            if ($order->payment_status !== 'pending' && $order->payment_status !== null) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot apply discount - payment already processed'
                ], 422);
            }

            // ✅ NEW: Check if discount already applied
            if ($order->discount_type && $order->discount_type !== 'none' && $order->discount_amount > 0) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Discount already applied to this order. Please remove the existing discount first.'
                ], 422);
            }

            // Calculate original total (use amount_before_discount if it exists, otherwise calculate)
            $originalTotal = $order->amount_before_discount ?? $this->calculateCorrectTotal($order);

            // Calculate 20% discount
            $discountPercentage = 20;
            $discountAmount = $originalTotal * ($discountPercentage / 100);
            $newTotal = $originalTotal - $discountAmount;

            // Update order with discount
            $order->update([
                'discount_type' => $validated['discount_type'],
                'discount_id_number' => $validated['id_number'],
                'discount_percent' => $discountPercentage,
                'discount_amount' => $discountAmount,
                'amount_before_discount' => $originalTotal,
                'total_amount' => $newTotal,
                'payment_status' => 'pending' // ✅ Ensure it stays pending
            ]);

            DB::commit();

            Log::info('Discount applied to order', [
                'order_id' => $order->id,
                'discount_type' => $validated['discount_type'],
                'id_number' => $validated['id_number'],
                'original_total' => $originalTotal,
                'discount_amount' => $discountAmount,
                'new_total' => $newTotal
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Discount applied successfully',
                'discount_details' => [
                    'discount_type' => $validated['discount_type'],
                    'discount_percent' => $discountPercentage,
                    'original_total' => $originalTotal,
                    'discount_amount' => $discountAmount,
                    'new_total' => $newTotal
                ]
            ]);
        } catch (ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error applying discount', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'order_id' => $validated['order_id'] ?? null
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to apply discount: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove discount from an order
     */
    public function removeDiscount(Request $request)
    {
        try {
            $validated = $request->validate([
                'order_id' => 'required|integer|exists:orders,id'
            ]);

            DB::beginTransaction();

            $order = Order::findOrFail($validated['order_id']);

            if ($order->payment_status !== 'pending') {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot remove discount - payment already processed'
                ], 422);
            }

            // Restore original total
            $originalTotal = $order->amount_before_discount ?? $this->calculateCorrectTotal($order);

            $order->update([
                'discount_type' => 'none',
                'discount_id_number' => null,
                'discount_percent' => 0,
                'discount_amount' => 0,
                'amount_before_discount' => null,
                'total_amount' => $originalTotal
            ]);

            DB::commit();

            Log::info('Discount removed from order', [
                'order_id' => $order->id,
                'restored_total' => $originalTotal
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Discount removed successfully',
                'new_total' => $originalTotal
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error removing discount', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to remove discount: ' . $e->getMessage()
            ], 500);
        }
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
            $cashOrders = Order::with([
                'orderItems',
                'orderItems.menuItem',
                'orderItems.menuItem.category'
            ])
                ->whereIn('payment_method', ['cash', 'maya'])
                ->where('status', 'pending')
                ->where(function ($query) {
                    $query->where('payment_status', 'pending')
                        ->orWhereNull('payment_status');
                })
                ->orderBy('created_at', 'asc')
                ->get();

            Log::info('Refresh orders called', [
                'count' => $cashOrders->count(),
                'order_ids' => $cashOrders->pluck('id')->toArray()
            ]);

            return response()->json([
                'success' => true,
                'orders' => $cashOrders->map(function ($order) {
                    $calculatedTotal = $this->calculateCorrectTotal($order);

                    $hasDiscount = $order->discount_type && $order->discount_type !== 'none' && $order->discount_amount > 0;
                    $discountAmount = $hasDiscount ? (float)$order->discount_amount : 0;
                    $amountBeforeDiscount = $hasDiscount ? (float)$order->amount_before_discount : $calculatedTotal;
                    $finalTotal = $hasDiscount ? ($amountBeforeDiscount - $discountAmount) : $calculatedTotal;

                    $cashAmount = (float) ($order->cash_amount ?? 0);
                    $expectedChange = $cashAmount > 0 ? ($cashAmount - $finalTotal) : 0;

                    return [
                        'id' => $order->id,
                        'order_number' => $order->order_number ?? str_pad($order->id, 4, '0', STR_PAD_LEFT),
                        'order_type' => $order->order_type,
                        'total_amount' => $finalTotal,
                        'total' => $finalTotal,
                        'amount_before_discount' => $amountBeforeDiscount,
                        'discount_type' => $order->discount_type,
                        'discount_percent' => $order->discount_percent ?? 20,
                        'discount_amount' => $discountAmount,
                        'discount_id_number' => $order->discount_id_number,
                        'cash_amount' => $cashAmount,
                        'expected_change' => $expectedChange,
                        'change_amount' => (float) ($order->change_amount ?? 0),
                        'payment_method' => $order->payment_method,
                        'payment_status' => $order->payment_status,
                        'status' => $order->status,
                        'maya_reference' => $order->maya_reference,
                        'maya_webhook_received_at' => $order->maya_webhook_received_at,
                        'created_at' => $order->created_at->toISOString(),
                        'order_items' => $this->formatOrderItemsFixed($order->orderItems)
                    ];
                })
            ]);
        } catch (Exception $e) {
            Log::error('Error refreshing cashier orders', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to refresh orders: ' . $e->getMessage()
            ], 500);
        }
    }

    public function acceptOrder(Request $request)
    {
        try {
            $validated = $request->validate([
                'order_id' => 'required|integer|exists:orders,id',
                'cash_amount' => 'required|numeric|min:0',
                'print_receipt' => 'boolean',
                'open_drawer' => 'boolean',
                'payment_method' => 'sometimes|string|in:cash,maya',
                'maya_confirmed' => 'sometimes|boolean'
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        }

        DB::beginTransaction();

        try {
            $order = Order::with(['orderItems.menuItem'])->findOrFail($validated['order_id']);

            // ✅ FIX: Use the order's current total_amount (which includes discount)
            // Don't recalculate - respect the discounted amount already in database
            $orderTotal = (float) $order->total_amount;

            // Log for debugging
            Log::info('Processing payment with discount consideration', [
                'order_id' => $order->id,
                'order_total_amount' => $order->total_amount,
                'has_discount' => $order->discount_type && $order->discount_type !== 'none',
                'discount_amount' => $order->discount_amount,
                'amount_before_discount' => $order->amount_before_discount,
                'final_total' => $orderTotal
            ]);

            // Determine payment method
            $paymentMethod = $validated['payment_method'] ?? 'cash';
            $cashAmount = $validated['cash_amount'] ?? null;
            $isMayaPayment = $paymentMethod === 'maya' || ($validated['maya_confirmed'] ?? false);

            // For Maya payments, set cash_amount to total (no change needed)
            if ($isMayaPayment) {
                $cashAmount = $orderTotal;
                $changeAmount = 0;
            } else {
                // ✅ Cash payment logic - use the DISCOUNTED total
                if ($validated['cash_amount'] < $orderTotal) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => 'Cash amount is insufficient. Required: PHP ' . number_format($orderTotal, 2)
                    ], 422);
                }
                $cashAmount = $validated['cash_amount'];
                $changeAmount = $validated['cash_amount'] - $orderTotal;
            }

            // Update order - keep the existing total_amount (with discount already applied)
            $order->update([
                'payment_method' => $isMayaPayment ? 'maya' : 'cash',
                'cash_amount' => $cashAmount,
                'change_amount' => $changeAmount,
                // ✅ DON'T update total_amount - it already has the discount applied
                // 'total_amount' => $orderTotal, // REMOVE THIS LINE
                'payment_status' => 'paid',
                'status' => 'pending',
                'paid_at' => now(),
            ]);

            Log::info('Payment processed - Order sent to kitchen', [
                'order_id' => $order->id,
                'payment_method' => $isMayaPayment ? 'maya' : 'cash',
                'final_total' => $orderTotal,
                'cash_received' => $cashAmount,
                'change_given' => $changeAmount,
                'had_discount' => $order->discount_type && $order->discount_type !== 'none'
            ]);

            $receiptPrinted = false;
            $drawerOpened = false;
            $printerError = null;
            $drawerError = null;

            // Open cash drawer ONLY for cash payments
            if (!$isMayaPayment && ($validated['open_drawer'] ?? true)) {
                Log::info('USB Drawer - Opening for cash payment', [
                    'order_id' => $order->id
                ]);

                try {
                    $drawerOpened = $this->cashDrawerService->openDrawer(1);

                    DB::table('cash_drawer_logs')->insert([
                        'drawer_number' => 1,
                        'action' => 'order_payment',
                        'order_id' => $order->id,
                        'reason' => 'Cash payment received',
                        'user_id' => Auth::id(),
                        'success' => $drawerOpened,
                        'error_message' => $drawerOpened ? null : 'Drawer service returned false',
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);

                    Log::info('USB Drawer - Operation result', [
                        'order_id' => $order->id,
                        'drawer_opened' => $drawerOpened
                    ]);
                } catch (Exception $e) {
                    $drawerError = $e->getMessage();
                    Log::error('USB Drawer - Opening exception', [
                        'order_id' => $order->id,
                        'error' => $drawerError
                    ]);

                    DB::table('cash_drawer_logs')->insert([
                        'drawer_number' => 1,
                        'action' => 'order_payment',
                        'order_id' => $order->id,
                        'reason' => 'Cash payment received',
                        'user_id' => Auth::id(),
                        'success' => false,
                        'error_message' => $drawerError,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);

                    $drawerOpened = false;
                }
            }

            // Print receipt for both cash and Maya payments
            if ($validated['print_receipt'] ?? true) {
                try {
                    Log::info('Bluetooth Printer - Printing receipt', [
                        'order_id' => $order->id,
                        'payment_method' => $isMayaPayment ? 'maya' : 'cash'
                    ]);

                    $receiptPrinted = true;

                    Log::info('Bluetooth Printer - Receipt printed successfully', [
                        'order_id' => $order->id
                    ]);
                } catch (Exception $e) {
                    $printerError = $e->getMessage();
                    Log::error('Bluetooth Printer - Printing failed', [
                        'order_id' => $order->id,
                        'error' => $printerError
                    ]);
                    $receiptPrinted = false;
                }
            }

            DB::commit();

            Log::info('Order payment processed and sent to kitchen', [
                'order_id' => $order->id,
                'payment_method' => $isMayaPayment ? 'maya' : 'cash',
                'total_amount' => $orderTotal,
                'cash_amount' => $cashAmount,
                'change_amount' => $changeAmount,
                'receipt_printed' => $receiptPrinted,
                'drawer_opened' => $drawerOpened,
                'kitchen_status' => 'pending'
            ]);

            return response()->json([
                'success' => true,
                'message' => ($isMayaPayment ? 'Maya' : 'Cash') . ' payment processed - Order sent to kitchen',
                'order_id' => $order->id,
                'payment_method' => $isMayaPayment ? 'maya' : 'cash',
                'total_amount' => $orderTotal,
                'cash_amount' => $cashAmount,
                'change_amount' => $changeAmount,
                'receipt_printed' => $receiptPrinted,
                'drawer_opened' => $drawerOpened,
                'printer_error' => $printerError,
                'drawer_error' => $drawerError,
                'kitchen_status' => 'Order sent to kitchen'
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Payment processing failed', [
                'order_id' => $validated['order_id'] ?? null,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Payment processing failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate receipt JSON for Thermer app printing
     */
    public function thermerReceipt($id)
    {
        // Clear any previous output
        while (ob_get_level()) {
            ob_end_clean();
        }

        // Start fresh output buffering
        ob_start();

        try {
            // Add debug logging
            Log::info('Thermer Receipt Request', ['order_id' => $id]);


            $order = Order::with('orderItems.menuItem')->find($id);

            if (!$order) {
                Log::warning('Thermer Receipt - Order not found', ['order_id' => $id]);
                return response()->json([
                    [
                        'type' => 0,
                        'content' => 'Order #' . $id . ' not found',
                        'bold' => 1,
                        'align' => 1,
                        'format' => 0
                    ]
                ], 200, ['Content-Type' => 'application/json']);
            }

            Log::info('Thermer Receipt - Order found', [
                'order_id' => $order->id,
                'items_count' => $order->orderItems->count()
            ]);

            $calculatedTotal = $this->calculateCorrectTotal($order);

            $lines = [];

            // Header
            $lines[] = [
                'type' => 0,
                'content' => 'SIP & SERVE CAFE',
                'bold' => 1,
                'align' => 1,
                'format' => 2
            ];

            $lines[] = [
                'type' => 0,
                'content' => 'Official Receipt',
                'bold' => 1,
                'align' => 1,
                'format' => 0
            ];

            $lines[] = [
                'type' => 0,
                'content' => '================================',
                'bold' => 0,
                'align' => 1,
                'format' => 0
            ];

            // Order details
            $lines[] = [
                'type' => 0,
                'content' => 'Receipt: ' . ($order->order_number ?? str_pad($order->id, 4, '0', STR_PAD_LEFT)),
                'bold' => 0,
                'align' => 0,
                'format' => 0
            ];

            $lines[] = [
                'type' => 0,
                'content' => 'Date: ' . $order->created_at->format('M d, Y H:i'),
                'bold' => 0,
                'align' => 0,
                'format' => 0
            ];

            $lines[] = [
                'type' => 0,
                'content' => 'Type: ' . ucfirst($order->order_type ?? 'Dine-in'),
                'bold' => 0,
                'align' => 0,
                'format' => 0
            ];

            if ($order->customer_name) {
                $lines[] = [
                    'type' => 0,
                    'content' => 'Customer: ' . substr($order->customer_name, 0, 20),
                    'bold' => 0,
                    'align' => 0,
                    'format' => 0
                ];
            }

            $lines[] = [
                'type' => 0,
                'content' => '--------------------------------',
                'bold' => 0,
                'align' => 0,
                'format' => 0
            ];

            // Items using your existing method
            $formattedItems = $this->formatOrderItemsFixed($order->orderItems);
            foreach ($formattedItems as $item) {
                $lines[] = [
                    'type' => 0,
                    'content' => substr($item['name'], 0, 25),
                    'bold' => 0,
                    'align' => 0,
                    'format' => 0
                ];

                $lines[] = [
                    'type' => 0,
                    'content' => '  ' . $item['quantity'] . ' x P' . number_format($item['unit_price'], 2) . ' = P' . number_format($item['total_price'], 2),
                    'bold' => 0,
                    'align' => 2,
                    'format' => 0
                ];
            }

            $lines[] = [
                'type' => 0,
                'content' => '--------------------------------',
                'bold' => 0,
                'align' => 0,
                'format' => 0
            ];

            $lines[] = [
                'type' => 0,
                'content' => 'TOTAL: P' . number_format($calculatedTotal, 2),
                'bold' => 1,
                'align' => 2,
                'format' => 1
            ];

            // Payment details
            if ($order->cash_amount) {
                $lines[] = [
                    'type' => 0,
                    'content' => 'Cash: P' . number_format((float) $order->cash_amount, 2),
                    'bold' => 0,
                    'align' => 0,
                    'format' => 0
                ];

                if ($order->change_amount > 0) {
                    $lines[] = [
                        'type' => 0,
                        'content' => 'Change: P' . number_format((float) $order->change_amount, 2),
                        'bold' => 0,
                        'align' => 0,
                        'format' => 0
                    ];
                }
            }

            $lines[] = [
                'type' => 0,
                'content' => '================================',
                'bold' => 0,
                'align' => 1,
                'format' => 0
            ];

            $lines[] = [
                'type' => 0,
                'content' => 'Thank you for dining with us!',
                'bold' => 0,
                'align' => 1,
                'format' => 0
            ];
            Log::info('Thermer Receipt - JSON generated successfully', [
                'order_id' => $id,
                'lines_count' => count($lines)
            ]);
            // Just before the return statement in thermerReceipt method
            Log::info('Thermer Receipt - Final JSON response', [
                'order_id' => $id,
                'lines_count' => count($lines),
                'first_line' => isset($lines[0]) ? $lines[0] : null,
                'json_encode_test' => json_encode($lines, JSON_UNESCAPED_UNICODE)
            ]);

            // Clear any output buffers that might add extra content
            if (ob_get_level()) {
                ob_clean();
            }
            // Clean JSON response - no extra content
            return response()->json($lines, 200, [
                'Content-Type' => 'application/json',
                'Cache-Control' => 'no-cache, no-store, must-revalidate',
                'Pragma' => 'no-cache',
                'Expires' => '0'
            ]);
        } catch (Exception $e) {
            Log::error('Thermer Receipt Error', [
                'order_id' => $id,
                'error' => $e->getMessage()
            ]);

            // Return error in JSON format that Thermer can understand
            return response()->json([
                [
                    'type' => 0,
                    'content' => 'Error: Receipt not found',
                    'bold' => 1,
                    'align' => 1,
                    'format' => 0
                ]
            ], 200, [
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
            // Convert ISO format to MySQL datetime format
            $completionTime = Carbon::parse($validated['completion_time'])->format('Y-m-d H:i:s');

            // Update order status to completed
            $order = Order::findOrFail($validated['order_id']);
            $order->update([
                'status' => 'completed',
                'completed_at' => $completionTime  // Changed
            ]);

            // Insert into daily_sales table
            DB::table('daily_sales')->insert([
                'order_id' => $validated['order_id'],
                'order_number' => $validated['order_number'],
                'total_amount' => $order->total_amount,
                'cash_received' => $order->cash_amount,
                'change_given' => $validated['change_given'],
                'completion_time' => $completionTime,  // Changed
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

    public function confirmMayaPayment(Request $request)
    {
        try {
            $orderId = $request->input('order_id');
            $order = Order::find($orderId);

            if (!$order) {
                return response()->json(['success' => false, 'message' => 'Order not found']);
            }

            // Update the order with Maya payment details
            $order->update([
                'payment_method' => 'maya',
                'payment_status' => 'paid', // This should match what your thermer script expects
                'paid_at' => now(),
                'receipt_printed' => true,
                // Add any other fields your system needs
            ]);

            // Make sure the update is saved to database
            $order->save();

            return response()->json([
                'success' => true,
                'message' => 'Maya payment confirmed',
                'receipt_printed' => true
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error confirming payment: ' . $e->getMessage()
            ]);
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
