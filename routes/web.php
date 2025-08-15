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

// CRITICAL: Cashier routes (moved to top for priority)
Route::prefix('cashier')->name('cashier.')->group(function () {
    Route::get('/', [CashierController::class, 'index'])->name('index');
    Route::get('/refresh', [CashierController::class, 'refreshOrders'])->name('refresh');
    Route::get('/stats', [CashierController::class, 'getDashboardStats'])->name('stats');
    
    // PAYMENT PROCESSING ROUTES (these are crucial)
    Route::post('/accept-order', [CashierController::class, 'acceptOrder'])->name('accept');
    Route::post('/cancel-order', [CashierController::class, 'cancelOrder'])->name('cancel');
    Route::post('/complete-order', [CashierController::class, 'completeOrder'])->name('complete');
    
    Route::get('/edit-order/{id}', function($id) {
        return redirect('/cashier')->with('message', "Edit functionality for order #{$id} coming soon");
    })->name('edit');
    
    Route::post('/create-manual-order', [CashierController::class, 'createManualOrder'])->name('createManual');
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

// Fallback test routes for debugging payment processing
Route::post('/cashier-fallback/accept', function (Request $request) {
    Log::info('Fallback cashier accept route called', [
        'data' => $request->all(),
        'csrf' => $request->header('X-CSRF-TOKEN')
    ]);
    
    $validated = $request->validate([
        'order_id' => 'required|integer',
        'cash_amount' => 'required|numeric|min:0',
        'print_receipt' => 'boolean'
    ]);
    
    // Simulate successful processing
    $change = $validated['cash_amount'] - 100; // Assume order total is 100 for testing
    
    return response()->json([
        'success' => true,
        'message' => 'Order accepted successfully (fallback)',
        'change_amount' => max(0, $change),
        'receipt_printed' => true,
        'order' => [
            'id' => $validated['order_id'],
            'status' => 'preparing',
            'payment_status' => 'paid'
        ]
    ]);
});

Route::post('/cashier-fallback/cancel', function (Request $request) {
    Log::info('Fallback cashier cancel route called', [
        'data' => $request->all()
    ]);
    
    $validated = $request->validate([
        'order_id' => 'required|integer',
        'reason' => 'nullable|string'
    ]);
    
    return response()->json([
        'success' => true,
        'message' => 'Order cancelled successfully (fallback)',
        'order_id' => $validated['order_id']
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
    
    // Payment processing
    Route::get('/payment', [KioskController::class, 'payment'])->name('payment');
    Route::post('/process-payment', [KioskController::class, 'processPayment'])->name('processPayment');
    Route::get('/payment/success', [KioskController::class, 'paymentSuccess'])->name('payment.success');
    Route::get('/payment/failed', [KioskController::class, 'paymentFailed'])->name('payment.failed');
});

// Kitchen routes (no auth required for demo)
Route::prefix('kitchen')->name('kitchen.')->group(function () {
    Route::get('/', [KioskController::class, 'kitchen'])->name('index');
    Route::post('/start/{id}', [KioskController::class, 'startOrder'])->name('start');
    Route::post('/complete/{id}', [KioskController::class, 'completeOrder'])->name('complete');
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

require __DIR__.'/auth.php';