<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\KioskController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\IngredientController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\NewPasswordController;

// Home route
Route::get('/', function () {
    return view('welcome');
});


Route::post('/ingredients/update', [IngredientController::class, 'updateStock']);

// Dashboard route
Route::get('/dashboard', [KioskController::class, 'dashboard'])->middleware(['auth', 'verified'])->name('dashboard');

// Sales and Product routes
Route::get('/sales', function () {
    return view('profile.sales');
})->name('sales');

Route::get('/product', [ProductController::class, 'index'])->name('products');
Route::post('/product', [ProductController::class, 'store'])->name('products.store');
Route::put('/product/{product}', [ProductController::class, 'update'])->name('products.update');
Route::delete('/product/{product}', [ProductController::class, 'destroy'])->name('products.destroy');

// Inventory route
Route::get('/inventory', function () {
    return view('profile.inventory');
})->name('inventory');

//review order route
Route::get('/review-order', function () {
   return view('reviewOrder');
})->name('review.order');


// Kiosk routes grouped together
Route::get('/category/{categoryId}/items', [KioskController::class, 'getCategoryItems'])->name('getCategoryItems');
Route::get('/category/{categoryId}/items', [KioskController::class, 'getCategoryItems'])->name('getCategoryItems');
Route::prefix('kiosk')->name('kiosk.')->group(function () {
    Route::get('/', [KioskController::class, 'index'])->name('index');
    Route::post('/dine-in', [KioskController::class, 'dineIn'])->name('dineIn');
    Route::post('/take-out', [KioskController::class, 'takeOut'])->name('takeOut');
    Route::get('/main', [KioskController::class, 'main'])->name('main');
    Route::post('/main', [KioskController::class, 'main'])->name('main.post');
    Route::get('/place-order', [KioskController::class, 'placeOrder'])->name('placeOrder');
    
    // New route for updating order type
    Route::post('/update-order-type', [KioskController::class, 'updateOrderType'])->name('updateOrderType');
    
    // Cart management
    Route::post('/cart/add', [KioskController::class, 'addToCart'])->name('addToCart');
    Route::delete('/cart/remove', [KioskController::class, 'removeFromCart'])->name('removeFromCart');
    Route::get('/cart', [KioskController::class, 'getCart'])->name('getCart');
    
    // Order processing
    Route::post('/submit-order', [KioskController::class, 'submitOrder'])->name('submitOrder');
    Route::get('/order-confirmation/{id}', [KioskController::class, 'orderConfirmation'])->name('orderConfirmation');
});

// Kitchen routes (separate from kiosk for better organization)
Route::prefix('kitchen')->name('kitchen.')->group(function () {
    Route::get('/', [KioskController::class, 'kitchen'])->name('index');
    Route::post('/start/{id}', [KioskController::class, 'startOrder'])->name('start');
    Route::post('/complete/{id}', [KioskController::class, 'completeOrder'])->name('complete');
});

//Cashier routes
Route::get('/cashier', function () {
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
});

Route::get('/kiosk/kitchen', [OrderController::class, 'kitchen'])->name('kiosk.kitchen');
Route::post('/order/{id}/start', [OrderController::class, 'startOrder'])->name('order.start');
Route::post('/order/{id}/complete', [OrderController::class, 'completeOrder'])->name('order.complete');

// Admin contact route (outside auth middleware so anyone can access)
Route::get('/adminContact', function () {
    return view('adminContact');
})->name('admin.contact');
// Forgot password Routes (outside auth middleware for guest access)
Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])
    ->middleware('guest')
    ->name('password.request');

Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])
    ->middleware('guest')
    ->name('password.email');

Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])
    ->middleware('guest')
    ->name('password.reset');

Route::post('reset-password', [NewPasswordController::class, 'store'])
    ->middleware('guest')
    ->name('password.store');

// Profile routes (these need authentication)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [LoginController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [LoginController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [LoginController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';