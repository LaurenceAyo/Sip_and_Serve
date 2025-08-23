<?php
//to generate QR on webpage: http://127.0.0.1:8000/generate-qr
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\KioskController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\IngredientController;
use App\Http\Controllers\MenuItemController;
use App\Http\Controllers\PaymongoWebhookController;
use App\Http\Controllers\CashierController;
use App\Http\Controllers\KitchenController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\NewPasswordController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

//Remove me If you don't need QR code generation or will be Deployed on Hostinger
Route::get('generate-qr', function () {
    $url = url('http://192.168.0.124:8000'); // Replace with your actual local IP and port
    return QrCode::size(300)->generate($url);
});

// Home route
Route::get('/', function () {
    return view('welcome');
})->name('home');

// PRIORITY: Thermal Printer Test Routes (for debugging)
Route::get('/test-printer-connection', function () {
    try {
        $thermalPrinterService = new App\Services\ThermalPrinterService();
        $connectionInfo = $thermalPrinterService->getConnectionInfo();

        return response()->json([
            'success' => true,
            'connection_info' => $connectionInfo,
            'message' => 'Printer service loaded successfully'
        ]);
    } catch (Exception $e) {
        return response()->json([
            'success' => false,
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
    }
});

Route::get('/test-printer-print', function () {
    try {
        $thermalPrinterService = new App\Services\ThermalPrinterService();
        $result = $thermalPrinterService->testPrinter();

        return response()->json([
            'success' => $result,
            'message' => $result ? 'Test print sent to printer!' : 'Test print failed - check logs',
            'connection_info' => $thermalPrinterService->getConnectionInfo()
        ]);
    } catch (Exception $e) {
        return response()->json([
            'success' => false,
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
    }
});

// CRITICAL: Cashier routes (moved to top for priority)
Route::prefix('cashier')->name('cashier.')->group(function () {
    Route::get('/', [CashierController::class, 'index'])->name('index');
    Route::get('/refresh', [CashierController::class, 'refreshOrders'])->name('refresh');
    Route::get('/stats', [CashierController::class, 'getDashboardStats'])->name('stats');

    // PAYMENT PROCESSING ROUTES (these are crucial)
    Route::post('/accept-order', [CashierController::class, 'acceptOrder'])->name('accept');
    Route::post('/cancel-order', [CashierController::class, 'cancelOrder'])->name('cancel');
    Route::post('/complete-order', [CashierController::class, 'completeOrder'])->name('complete');
    Route::post('/update-order', [CashierController::class, 'updateOrder'])->name('update');

    Route::get('/edit-order/{id}', function ($id) {
        return redirect('/cashier')->with('message', "Edit functionality for order #{$id} coming soon");
    })->name('edit');

    Route::post('/create-manual-order', [CashierController::class, 'createManualOrder'])->name('createManual');
});

// Additional printer test routes
Route::get('/test-printer', function () {
    $thermalPrinterService = new App\Services\ThermalPrinterService();
    $result = $thermalPrinterService->testPrinter();

    return response()->json([
        'success' => $result,
        'message' => $result ? 'Printer test successful!' : 'Printer test failed - check logs',
        'connection_info' => $thermalPrinterService->getConnectionInfo()
    ]);
});

Route::get('/printer-info', function () {
    $thermalPrinterService = new App\Services\ThermalPrinterService();

    return response()->json([
        'connection_info' => $thermalPrinterService->getConnectionInfo()
    ]);
});

Route::get('/test-receipt/{orderId}', function ($orderId) {
    $order = App\Models\Order::with(['orderItems.menuItem'])->find($orderId);

    if (!$order) {
        return response()->json(['error' => 'Order not found'], 404);
    }

    $thermalPrinterService = new App\Services\ThermalPrinterService();
    $result = $thermalPrinterService->printReceipt($order);

    return response()->json([
        'success' => $result,
        'message' => $result ? 'Receipt printed successfully!' : 'Receipt printing failed - check logs',
        'order_id' => $orderId,
        'connection_info' => $thermalPrinterService->getConnectionInfo()
    ]);
});

// Debug routes for troubleshooting (remove in production)
Route::get('/debug-cashier', function () {
    try {
        $controller = new App\Http\Controllers\CashierController();
        return response()->json([
            'success' => true,
            'message' => 'CashierController loaded successfully',
            'methods' => get_class_methods($controller),
            'csrf_token' => csrf_token(),
            'route_check' => [
                'accept_order' => url('/cashier/accept-order'),
                'cancel_order' => url('/cashier/cancel-order'),
                'refresh' => url('/cashier/refresh'),
                'complete_order' => url('/cashier/complete-order')
            ]
        ]);
    } catch (Exception $e) {
        return response()->json([
            'success' => false,
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
    }
});

// Test route to verify POST is working
Route::post('/test-cashier-post', function (Request $request) {
    Log::info('Test cashier POST route called', [
        'data' => $request->all(),
        'headers' => $request->headers->all()
    ]);

    return response()->json([
        'success' => true,
        'message' => 'POST request working',
        'received_data' => $request->all(),
        'csrf_token' => csrf_token(),
        'timestamp' => now()
    ]);
});

// Public routes - Kiosk system (no auth required)
Route::prefix('kiosk')->name('kiosk.')->group(function () {
    Route::get('/', [KioskController::class, 'index'])->name('index');
    Route::post('/dine-in', [KioskController::class, 'dineIn'])->name('dineIn');
    Route::post('/take-out', [KioskController::class, 'takeOut'])->name('takeOut');
    Route::get('/main', [KioskController::class, 'main'])->name('main');
    Route::post('/main', [KioskController::class, 'main'])->name('main.post');
    Route::get('/place-order', [KioskController::class, 'placeOrder'])->name('placeOrder');
    Route::post('/update-order-type', [KioskController::class, 'updateOrderType'])->name('updateOrderType');

    Route::post('/process-cash-payment', [KioskController::class, 'processCashPayment'])->name('processCashPayment');

    // Cart management
    Route::post('/cart/add', [KioskController::class, 'addToCart'])->name('addToCart');
    Route::delete('/cart/remove', [KioskController::class, 'removeFromCart'])->name('removeFromCart');
    Route::get('/cart', [KioskController::class, 'getCart'])->name('getCart');
    Route::post('/update-cart-item', [KioskController::class, 'updateCartItem'])->name('updateCartItem');
    Route::post('/remove-cart-item', [KioskController::class, 'removeCartItem'])->name('removeCartItem');

    // Order processing
    Route::get('/review-order', [KioskController::class, 'reviewOrder'])->name('reviewOrder');
    Route::post('/checkout', [KioskController::class, 'checkout'])->name('checkout');
    Route::post('/cancel-order', [KioskController::class, 'cancelOrder'])->name('cancelOrder');
    Route::post('/process-order', [KioskController::class, 'processOrder'])->name('processOrder');
    Route::post('/submit-order', [KioskController::class, 'submitOrder'])->name('submitOrder');
    Route::get('/order-confirmation/{id?}', [KioskController::class, 'orderConfirmation'])->name('orderConfirmation');

    // Payment processing - FIXED: Move cash payment route here
    Route::get('/payment', [KioskController::class, 'payment'])->name('payment');
    Route::post('/process-payment', [KioskController::class, 'processPayment'])->name('processPayment');
    Route::post('/process-cash-payment', [KioskController::class, 'processCashPayment'])->name('processCashPayment');
    Route::get('/payment/success', [KioskController::class, 'paymentSuccess'])->name('payment.success');
    Route::get('/payment/failed', [KioskController::class, 'paymentFailed'])->name('payment.failed');
});

Route::prefix('kitchen')->name('kitchen.')->group(function () {
    Route::get('/', [KitchenController::class, 'index'])->name('index');
    Route::post('/start/{order}', [KitchenController::class, 'start'])->name('start');
    Route::post('/complete/{order}', [KitchenController::class, 'completeOrder'])->name('completeOrder');
    Route::post('/receive/{order}', [KitchenController::class, 'receiveOrder'])->name('receiveOrder'); // Legacy

    // Add this to your routes/web.php file
    Route::get('/kitchen/data', [KitchenController::class, 'getData'])->name('kitchen.data');

    
});

// Public API routes
Route::get('/category/{categoryId}/items', [KioskController::class, 'getCategoryItems'])->name('getCategoryItems');
Route::post('/send-receipt', [OrderController::class, 'sendReceipt'])->name('send.receipt');
Route::get('/product', [KioskController::class, 'product'])->name('product');

// AJAX operations for menu items (admin functions)
Route::post('/menu-items/store', [KioskController::class, 'storeMenuItem'])->name('menu-items.store');
Route::post('/menu-items/update', [KioskController::class, 'updateMenuItem'])->name('menu-items.update');
Route::post('/menu-items/delete', [KioskController::class, 'deleteMenuItem'])->name('menu-items.delete');
Route::post('/ingredients/update', [IngredientController::class, 'updateStock'])->name('ingredients.update');

// PayMongo Webhook (public route)
Route::post('/webhooks/paymongo', [PaymongoWebhookController::class, 'handleWebhook'])->name('paymongo.webhook');

// Guest routes (password reset)
Route::middleware('guest')->group(function () {
    Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])->name('password.request');
    Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])->name('password.email');
    Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])->name('password.reset');
    Route::post('reset-password', [NewPasswordController::class, 'store'])->name('password.store');
});

// Public contact route
Route::get('/adminContact', function () {
    return view('adminContact');
})->name('admin.contact');


// Add this to your web.php
Route::get('/test-bluetooth-printer', function () {
    try {
        Log::info('Testing Bluetooth POS58 printer: PT-210_BEE1');

        // Method 1: Try direct connection
        $printerNames = [
            'PT-210_BEE1',
            'GoojPRT PT-210',
            'POS58 Printer'
        ];

        $results = [];

        foreach ($printerNames as $printerName) {
            try {
                Log::info("Attempting connection to: $printerName");

                $connector = new Mike42\Escpos\PrintConnectors\WindowsPrintConnector($printerName);
                $printer = new Mike42\Escpos\Printer($connector);

                // Send simple test
                $printer->initialize();
                $printer->text("BLUETOOTH TEST\n");
                $printer->text("Printer: $printerName\n");
                $printer->text("Time: " . now()->format('H:i:s') . "\n");
                $printer->text("Status: Connected\n");
                $printer->feed(3);

                try {
                    $printer->cut();
                } catch (Exception $e) {
                    $printer->feed(2);
                }

                $printer->close();

                $results[$printerName] = 'SUCCESS - Check printer';
                Log::info("SUCCESS: Printed to $printerName");
            } catch (Exception $e) {
                $results[$printerName] = 'FAILED: ' . $e->getMessage();
                Log::error("FAILED: $printerName - " . $e->getMessage());
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Bluetooth printer test completed',
            'results' => $results,
            'instructions' => 'Check your PT-210 printer for test receipts'
        ]);
    } catch (Exception $e) {
        Log::error('Bluetooth printer test failed', [
            'error' => $e->getMessage()
        ]);

        return response()->json([
            'success' => false,
            'error' => $e->getMessage()
        ]);
    }
});

// Authenticated routes (admin panel)
Route::middleware('auth')->group(function () {
    // Dashboard
    Route::get('/dashboard', [KioskController::class, 'dashboard'])->middleware('verified')->name('dashboard');

    // Sales & Inventory
    Route::get('/sales', function () {
        return view('profile.sales');
    })->name('sales');

    Route::get('/inventory', [KioskController::class, 'dashboard'])->name('inventory');

    // Profile management
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Development/Testing routes (remove in production)
    Route::get('/webhooks/paymongo/test', [PaymongoWebhookController::class, 'testWebhook'])->name('paymongo.webhook.test');
});

require __DIR__ . '/auth.php';