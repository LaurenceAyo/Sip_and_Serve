<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\KioskController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\IngredientController;
use App\Http\Controllers\MenuItemController;
use App\Http\Controllers\PaymongoWebhookController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\NewPasswordController;

// Home route
Route::get('/', function () {
    return view('welcome');
})->name('home');

// AJAX operations for menu items
Route::post('/menu-items/store', [KioskController::class, 'storeMenuItem'])->name('menu-items.store');
Route::post('/menu-items/update', [KioskController::class, 'updateMenuItem'])->name('menu-items.update');
Route::post('/menu-items/delete', [KioskController::class, 'deleteMenuItem'])->name('menu-items.delete');

// Product route
Route::get('/product', [KioskController::class, 'product'])->name('product');

// Ingredients route
Route::post('/ingredients/update', [IngredientController::class, 'updateStock'])->name('ingredients.update');

// Category items route
Route::get('/category/{categoryId}/items', [KioskController::class, 'getCategoryItems'])->name('getCategoryItems');

// Kiosk routes grouped together
Route::prefix('kiosk')->name('kiosk.')->group(function () {
    Route::get('/', [KioskController::class, 'index'])->name('index');
    Route::post('/dine-in', [KioskController::class, 'dineIn'])->name('dineIn');
    Route::post('/take-out', [KioskController::class, 'takeOut'])->name('takeOut');
    Route::get('/main', [KioskController::class, 'main'])->name('main');
    Route::post('/main', [KioskController::class, 'main'])->name('main.post');
    Route::get('/place-order', [KioskController::class, 'placeOrder'])->name('placeOrder');
    
    // Route for updating order type
    Route::post('/update-order-type', [KioskController::class, 'updateOrderType'])->name('updateOrderType');
    
    // Cart management
    Route::post('/cart/add', [KioskController::class, 'addToCart'])->name('addToCart');
    Route::delete('/cart/remove', [KioskController::class, 'removeFromCart'])->name('removeFromCart');
    Route::get('/cart', [KioskController::class, 'getCart'])->name('getCart');
    Route::post('/update-cart-item', [KioskController::class, 'updateCartItem'])->name('updateCartItem');
    Route::post('/remove-cart-item', [KioskController::class, 'removeCartItem'])->name('removeCartItem');
    
    // Order review and checkout routes
    Route::get('/review-order', [KioskController::class, 'reviewOrder'])->name('reviewOrder');
    Route::post('/checkout', [KioskController::class, 'checkout'])->name('checkout');
    Route::post('/cancel-order', [KioskController::class, 'cancelOrder'])->name('cancelOrder');
    Route::post('/process-order', [KioskController::class, 'processOrder'])->name('processOrder');
    
    // Order processing
    Route::post('/submit-order', [KioskController::class, 'submitOrder'])->name('submitOrder');
    Route::get('/order-confirmation/{id?}', [KioskController::class, 'orderConfirmation'])->name('orderConfirmation');
    Route::get('/payment', [KioskController::class, 'payment'])->name('payment');
    
    // Payment processing routes
    Route::post('/process-payment', [KioskController::class, 'processPayment'])->name('processPayment');
    Route::get('/payment/success', [KioskController::class, 'paymentSuccess'])->name('payment.success');
    Route::get('/payment/failed', [KioskController::class, 'paymentFailed'])->name('payment.failed');
});

// Kitchen routes (separate from kiosk for better organization)
Route::prefix('kitchen')->name('kitchen.')->group(function () {
    Route::get('/', [KioskController::class, 'kitchen'])->name('index');
    Route::post('/start/{id}', [KioskController::class, 'startOrder'])->name('start');
    Route::post('/complete/{id}', [KioskController::class, 'completeOrder'])->name('complete');
});

// Cashier routes
Route::prefix('cashier')->name('cashier.')->group(function () {
    Route::get('/', function () {
        $pendingOrders = [
            [
                'id' => '0001',
                'time' => '8:30',
                'items' => [
                    ['name' => 'Pad Thai x1', 'price' => 250.00],
                    ['name' => 'Cappuccino x1', 'price' => 150.00]
                ],
                'total' => 400.00
            ]
            // ... more orders
        ];
        
        return view('cashier', compact('pendingOrders'));
    })->name('index');
});

// PayMongo Webhook (must be outside auth middleware)
Route::post('/webhooks/paymongo', [PaymongoWebhookController::class, 'handleWebhook'])->name('paymongo.webhook');

// Email Receipt Route (FIXED: Moved outside auth middleware)
Route::post('/send-receipt', [OrderController::class, 'sendReceipt'])->name('send.receipt');

// Admin contact route (outside auth middleware so anyone can access)
Route::get('/adminContact', function () {
    return view('adminContact');
})->name('admin.contact');

// Forgot password Routes (outside auth middleware for guest access)
Route::middleware('guest')->group(function () {
    Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])->name('password.request');
    Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])->name('password.email');
    Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])->name('password.reset');
    Route::post('reset-password', [NewPasswordController::class, 'store'])->name('password.store');
});

// Authenticated routes
Route::middleware('auth')->group(function () {
    // Dashboard route
    Route::get('/dashboard', [KioskController::class, 'dashboard'])->middleware('verified')->name('dashboard');
    
    // Sales route
    Route::get('/sales', function () {
        return view('profile.sales');
    })->name('sales');
    
    // Inventory route
    Route::get('/inventory', [KioskController::class, 'dashboard'])->name('inventory');
    
    // Profile routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    //CAUTION: REMOVE THIS LINE AFTER DEVELOPMENT IS DONE
    // Test webhook route (development only)
    Route::get('/webhooks/paymongo/test', [PaymongoWebhookController::class, 'testWebhook'])->name('paymongo.webhook.test');
});

require __DIR__.'/auth.php';