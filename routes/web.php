<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\KioskController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\IngredientController;
use App\Http\Controllers\MenuItemController;
use App\Http\Controllers\PaymongoWebhookController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\QRPaymentController;
use App\Http\Controllers\CashierController;
use App\Http\Controllers\KitchenController;
use App\Http\Controllers\SalesController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Pos\PosAuthController;
use App\Http\Controllers\BackupSettingsController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use App\Http\Controllers\POSPaymentController;
use Illuminate\Support\Facades\Auth;
use App\Models\Order;
use Ladumor\LaravelPwa\LaravelPWA;
use App\Http\Controllers\PrinterController;

// =============================================================================
// PUBLIC ROUTES (No Authentication Required)
// =============================================================================

// Home and Login Routes
Route::get('/', [LoginController::class, 'showLoginForm'])->name('home');
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Public Contact Route
Route::get('/adminContact', function () {
    return view('adminContact');
})->name('admin.contact');

//==============================================================================
// PWA routes
//==============================================================================
Route::get('/offline', function () {
    return view('pwa.offline');
})->name('offline');

Route::get('/manifest.json', [LaravelPWA::class, 'manifest'])->name('pwa.manifest');
Route::get('/sw.js', [LaravelPWA::class, 'sw'])->name('pwa.sw');


//==============================================================================
// WIFI PRINTER TEST ROUTES
//==============================================================================
Route::post('/cashier/print-wifi-receipt', [CashierController::class, 'printWiFiReceipt']);
Route::post('/cashier/test-wifi-printer', [CashierController::class, 'testWiFiPrinter']);
Route::get('/cashier/wifi-printer-status', [CashierController::class, 'getWiFiPrinterStatus']);
Route::post('/cashier/update-wifi-printer-ip', [CashierController::class, 'updateWiFiPrinterIP']);

//==============================================================================
// Password Reset Routes (Guest only)
//==============================================================================
Route::middleware('guest')->group(function () {
    Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])->name('password.request');
    Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])->name('password.email');
    Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])->name('password.reset');
    Route::post('reset-password', [NewPasswordController::class, 'store'])->name('password.store');
});

// =============================================================================
// KIOSK SYSTEM (Public - No Auth Required)
// =============================================================================
Route::prefix('kiosk')->name('kiosk.')->group(function () {
    Route::get('/', [KioskController::class, 'index'])->name('index');
    Route::post('/dine-in', [KioskController::class, 'dineIn'])->name('dineIn');
    Route::post('/take-out', [KioskController::class, 'takeOut'])->name('takeOut');
    Route::get('/main', [KioskController::class, 'main'])->name('main');
    Route::post('/main', [KioskController::class, 'main'])->name('main.post');
    Route::get('/place-order', [KioskController::class, 'placeOrder'])->name('placeOrder');
    Route::post('/update-order-type', [KioskController::class, 'updateOrderType'])->name('updateOrderType');

    // Cart Management
    Route::post('/cart/add', [KioskController::class, 'addToCart'])->name('addToCart');
    Route::delete('/cart/remove', [KioskController::class, 'removeFromCart'])->name('removeFromCart');
    Route::get('/cart', [KioskController::class, 'getCart'])->name('getCart');
    Route::post('/update-cart-item', [KioskController::class, 'updateCartItem'])->name('updateCartItem');
    Route::post('/remove-cart-item', [KioskController::class, 'removeCartItem'])->name('removeCartItem');

    // Order Processing
    Route::get('/review-order', [KioskController::class, 'reviewOrder'])->name('reviewOrder');
    Route::post('/checkout', [KioskController::class, 'checkout'])->name('checkout');
    Route::post('/cancel-order', [KioskController::class, 'cancelOrder'])->name('cancelOrder');
    Route::post('/process-order', [KioskController::class, 'processOrder'])->name('processOrder');
    Route::post('/submit-order', [KioskController::class, 'submitOrder'])->name('submitOrder');
    Route::get('/order-confirmation/{id?}', [KioskController::class, 'orderConfirmation'])->name('orderConfirmation');
    Route::get('/order-confirmation-success', [KioskController::class, 'orderConfirmationSuccess'])->name('orderConfirmationSuccess');

    // Payment Processing
    Route::get('/payment', [KioskController::class, 'payment'])->name('payment');
    Route::post('/process-payment', [KioskController::class, 'processPayment'])->name('processPayment');
    Route::post('/process-cash-payment', [PaymentController::class, 'processCashPayment'])->name('processCashPayment');
    Route::post('/process-gcash-payment', [KioskController::class, 'processGCashPayment'])->name('processGCashPayment');

    // Payment Result Pages
    Route::get('/payment-success', [KioskController::class, 'paymentSuccess'])->name('paymentSuccess');
    Route::get('/payment-failed', [KioskController::class, 'paymentFailed'])->name('paymentFailed');
    Route::get('/payment/success', [KioskController::class, 'paymentSuccess'])->name('payment.success');
    Route::get('/payment/failed', [KioskController::class, 'paymentFailed'])->name('payment.failed');
});

// =============================================================================
// QR PAYMENT SYSTEM (Public)
// =============================================================================
Route::prefix('qr')->name('qr.')->group(function () {
    Route::get('/payment/{orderId}', [QRPaymentController::class, 'showQRPaymentPage'])->name('payment.show');
    Route::get('/payment/{orderId}/generate', [QRPaymentController::class, 'generateOrderQR'])->name('payment.generate');
    Route::get('/payment/{orderId}/status', [QRPaymentController::class, 'checkPaymentStatus'])->name('payment.status');
    Route::get('/payment/process/{orderId}/{paymentIntentId}', [QRPaymentController::class, 'processQRPayment'])->name('payment.process');
});

// =============================================================================
// API ROUTES (Public)
// =============================================================================
Route::prefix('api')->group(function () {
    // POS Payment API
    Route::prefix('pos/payment')->group(function () {
        Route::post('/process', [POSPaymentController::class, 'processPayment']);
        Route::get('/status/{paymentIntentId}', [POSPaymentController::class, 'checkPaymentStatus']);
    });

    // Inventory API
    Route::get('/inventory', [InventoryController::class, 'getInventoryData'])->name('api.inventory');
});

// =============================================================================
// WEBHOOK ROUTES (Public - No CSRF)
// =============================================================================
Route::post('/paymongo/webhook', [POSPaymentController::class, 'handleWebhook']);
Route::post('/webhooks/paymongo', [PaymongoWebhookController::class, 'handleWebhook'])->name('paymongo.webhook');

// =============================================================================
// PUBLIC API & UTILITY ROUTES
// =============================================================================
Route::get('/category/{categoryId}/items', [KioskController::class, 'getCategoryItems'])->name('getCategoryItems');
Route::post('/send-receipt', [OrderController::class, 'sendReceipt'])->name('send.receipt');

// =============================================================================
// AUTHENTICATED ROUTES WITH ROLE-BASED ACCESS CONTROL
// =============================================================================

// Basic authenticated routes (any authenticated user)
Route::middleware(['auth'])->group(function () {

    // =============================================================================
    // PIN AUTHENTICATION SYSTEM
    // =============================================================================
    Route::get('/pin-login', [AuthController::class, 'showPinLogin'])->name('pin.login');
    Route::post('/pin-login', [AuthController::class, 'pinLogin'])->name('pin.authenticate');
    Route::post('/pin-logout', [AuthController::class, 'pinLogout'])->name('pin.logout');

    // POS PIN routes
    Route::get('/pos/pin/setup', [PosAuthController::class, 'showPinSetup'])->name('pos.pin.setup');
    Route::post('/pos/pin/setup', [PosAuthController::class, 'setupPin']);
    Route::get('/pos/pin/verify', [PosAuthController::class, 'showPinVerify'])->name('pos.pin.verify');
    Route::post('/pos/pin/verify', [PosAuthController::class, 'verifyPin']);
    Route::post('/pos/lock', [PosAuthController::class, 'lockPos'])->name('pos.lock');
    Route::post('/pos/pin/reset', [PosAuthController::class, 'resetPin'])->name('pos.pin.reset');

    // Profile Management (All authenticated users)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// =============================================================================
// ADMIN ROUTES (Admin only)
// =============================================================================
Route::middleware(['auth'])->group(function () {
    Route::prefix('admin')->name('admin.')->group(function () {
        // User Management Routes
        Route::get('/users', [AdminController::class, 'userManagement'])->name('users');
        Route::get('/users/data', [AdminController::class, 'getUsersData'])->name('users.data');
        Route::post('/users', [AdminController::class, 'createUser'])->name('users.create');
        Route::post('/users/{id}/reset-password', [AdminController::class, 'resetPassword'])->name('users.reset-password');
        Route::put('/users/{id}', [AdminController::class, 'updateUser'])->name('users.update');
        Route::delete('/users/{id}', [AdminController::class, 'deleteUser'])->name('users.delete');
        Route::get('/users/{id}', [AdminController::class, 'getUserDetails'])->name('users.details');

        // Backup Settings
        Route::get('/backup-settings', [BackupSettingsController::class, 'index'])->name('backup-settings');
        Route::put('/backup-settings', [BackupSettingsController::class, 'update'])->name('backup-settings.update');
        Route::get('/backup', [BackupSettingsController::class, 'backup'])->name('backup');
        Route::post('/restore-backup', [BackupSettingsController::class, 'restore']);
    });
});

// =============================================================================
// MANAGER ROUTES (Manager and Admin)
// =============================================================================
Route::middleware(['auth'])->group(function () {
    // Dashboard (Inventory Management)
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/inventory', [InventoryController::class, 'index'])->name('inventory');

    // Sales Management
    Route::get('/sales', [SalesController::class, 'index'])->name('sales');

    // Product Management
    Route::get('/product', [ProductController::class, 'index'])->name('product');

    // Menu & Ingredient Management
    Route::post('/menu-items/store', [KioskController::class, 'storeMenuItem'])->name('menu-items.store');
    Route::post('/menu-items/update', [KioskController::class, 'updateMenuItem'])->name('menu-items.update');
    Route::post('/menu-items/delete', [KioskController::class, 'deleteMenuItem'])->name('menu-items.delete');
    Route::post('/ingredients/update', [IngredientController::class, 'updateStock'])->name('ingredients.update');
});

// =============================================================================
// CASHIER ROUTES (Cashier, Manager, and Admin)
// =============================================================================
Route::middleware(['auth'])->group(function () {
    Route::prefix('cashier')->name('cashier.')->group(function () {
        Route::get('/', [CashierController::class, 'index'])->name('index');
        Route::get('/refresh', [CashierController::class, 'refreshOrders'])->name('refresh');
        Route::get('/stats', [CashierController::class, 'getDashboardStats'])->name('stats');

        // Order Management
        Route::post('/accept-order', [CashierController::class, 'acceptOrder'])->name('accept');
        Route::post('/cancel-order', [CashierController::class, 'cancelOrder'])->name('cancel');
        Route::post('/complete-order', [CashierController::class, 'completeOrder'])->name('complete');
        Route::post('/update-order', [CashierController::class, 'updateOrder'])->name('update');
        Route::post('/create-manual-order', [CashierController::class, 'createManualOrder'])->name('createManual');

        Route::get('/edit-order/{id}', function ($id) {
            return redirect('/cashier')->with('message', "Edit functionality for order #{$id} coming soon");
        })->name('edit');
    });



    Route::get('/simple-thermer/{id}', [App\Http\Controllers\CashierController::class, 'simpleThermerTest']);
    Route::get('/thermer/receipt/{id}', [App\Http\Controllers\CashierController::class, 'thermerReceipt'])->name('thermer.receipt');
    Route::get('/printer/receipt/{id}', [App\Http\Controllers\PrinterJsonController::class, 'receipt'])
    ->name('printer.receipt');
    Route::get('/receipt/{id}', [PrinterController::class, 'receipt'])->name('receipt.print');
    Route::get('/printer/response',[App\Http\Controllers\PrinterController::class,'response']);
    Route::get('/printer/json/{id}', [App\Http\Controllers\PrinterJsonController::class, 'receipt']);
    Route::get('/printer/next', function () {
        $orderId = Cache::pull('thermer_print_queue'); // Get and remove from queue
        if ($orderId) {
            return redirect("/printer/json/{$orderId}");
        }
        return response()->json(['message' => 'No orders to print'], 404);
    });

    // Cash drawer operations
    Route::post('/cashier/drawer/open', [CashierController::class, 'openDrawer']);
    Route::get('/cashier/drawer/test', [CashierController::class, 'testDrawer']);
    Route::get('/cashier/drawer/diagnostics', [CashierController::class, 'drawerDiagnostics']);

    // Order status updates
    Route::post('/orders/{id}/complete', [OrderController::class, 'markCompleted'])->name('orders.complete');
    Route::post('/orders/{id}/status', [OrderController::class, 'updateStatus'])->name('orders.status');
});

// =============================================================================
// KITCHEN ROUTES (Kitchen Staff, Manager, and Admin)
// =============================================================================
Route::middleware(['auth'])->group(function () {
    Route::prefix('kitchen')->name('kitchen.')->group(function () {
        Route::get('/', [KitchenController::class, 'index'])->name('index');
        Route::get('/data', [KitchenController::class, 'getData'])->name('data');
        Route::post('/start/{order}', [KitchenController::class, 'start'])->name('start');
        Route::post('/complete/{order}', [KitchenController::class, 'completeOrder'])->name('completeOrder');
        Route::post('/receive/{order}', [KitchenController::class, 'receiveOrder'])->name('receiveOrder');
    });
});

// =============================================================================
// STAFF ROUTES (Any authenticated staff member)
// =============================================================================
Route::middleware(['auth'])->group(function () {
    // Basic kiosk access for authenticated staff
    Route::prefix('kiosk')->name('kiosk.')->group(function () {
        Route::get('/', [KioskController::class, 'index'])->name('index');
        Route::get('/main', [KioskController::class, 'main'])->name('main');
        Route::get('/review-order', [KioskController::class, 'reviewOrder'])->name('reviewOrder');
        Route::post('/checkout', [KioskController::class, 'checkout'])->name('checkout');
        Route::post('/submit-order', [KioskController::class, 'submitOrder'])->name('submitOrder');
        Route::get('/order-confirmation/{id?}', [KioskController::class, 'orderConfirmation'])->name('orderConfirmation');
        Route::post('/cart/add', [KioskController::class, 'addToCart'])->name('addToCart');
        Route::post('/process-order', [KioskController::class, 'processOrder'])->name('processOrder');
    });
});

// =============================================================================
// PRINTER TESTING ROUTES (Staff only)
// =============================================================================
//Thermal app Bridge
Route::get('/printer/response', [App\Http\Controllers\PrinterController::class, 'response']);
Route::get('/printer/json/{id}', [App\Http\Controllers\PrinterJsonController::class, 'receipt']);

// Bluetooth printing routes
//Route::post('/cashier/receipt-content', [CashierController::class, 'getReceiptContent']);
//Route::post('/cashier/web-bluetooth-test', [CashierController::class, 'webBluetoothTest']);
//Route::get('/cashier/bluetooth-support', [CashierController::class, 'checkBluetoothSupport']);
//Route::get('/cashier/bluetooth-print', [CashierController::class, 'bluetoothPrintReceipt']);


// Additional printer diagnostics
Route::get('/cashier/printer-diagnostics', [CashierController::class, 'printerDiagnostics']);
Route::post('/cashier/test-bluetooth-print', [CashierController::class, 'webBluetoothTest']);

Route::middleware(['auth'])->group(function () {
    Route::post('/print-receipt', [App\Http\Controllers\PrintController::class, 'printReceipt']);
    Route::post('/test-printer', [App\Http\Controllers\PrintController::class, 'testPrinter']);
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
        $thermalPrinterService->printReceipt($order);
        return response()->json([
            'success' => true,
            'message' => 'Receipt printed successfully!',
            'order_id' => $orderId,
            'connection_info' => $thermalPrinterService->getConnectionInfo()
        ]);
    });

    Route::get('/test-bluetooth-printer', function () {
        try {
            Log::info('Testing Bluetooth POS58 printer: PT-210_BEE1');
            $printerNames = ['PT-210_BEE1', 'GoojPRT PT-210', 'POS58 Printer'];
            $results = [];
            foreach ($printerNames as $printerName) {
                try {
                    Log::info("Attempting connection to: $printerName");
                    $connector = new Mike42\Escpos\PrintConnectors\WindowsPrintConnector($printerName);
                    $printer = new Mike42\Escpos\Printer($connector);
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
            Log::error('Bluetooth printer test failed', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'error' => $e->getMessage()]);
        }
    });
});

// =============================================================================
// DEBUG & TESTING ROUTES (Remove in Production)
// =============================================================================
Route::middleware(['auth'])->group(function () {
    Route::get('/debug-auth', function () {
        return response()->json([
            'authenticated' => Auth::check(),
            'user' => Auth::user(),
            'session_id' => session()->getId(),
            'current_route' => request()->path()
        ]);
    });

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

    Route::get('/webhooks/paymongo/test', [PaymongoWebhookController::class, 'testWebhook'])->name('paymongo.webhook.test');
});

require __DIR__ . '/auth.php';
