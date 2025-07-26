<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\KioskController;
use App\Http\Controllers\OrderController;
use Illuminate\Support\Facades\Route;

// Home route
Route::get('/', function () {
    return view('welcome');
});

// Login routes
Route::get('/login', [LoginController::class, 'showLoginForm'])->middleware('guest')->name('login');
Route::post('/login', [LoginController::class, 'login']);

// Dashboard route
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Admin dashboard
Route::get('/admin/sip-serve-dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('admin.dashboard');

// Sales and Product routes
Route::get('/sales', function () {
    return view('profile.sales');
})->name('sales');

Route::get('/product', function () {
    return view('profile.product');
})->name('product');

// Kiosk routes grouped together

Route::match(['GET', 'POST'], '/kiosk/main', [KioskController::class, 'main'])->name('kiosk.main');
Route::prefix('kiosk')->name('kiosk.')->group(function () {
    Route::get('/', [KioskController::class, 'index'])->name('index');
    Route::post('/dine-in', [KioskController::class, 'dineIn'])->name('dineIn');
    Route::post('/take-out', [KioskController::class, 'takeOut'])->name('takeOut');
    Route::get('/main', [KioskController::class, 'main'])->name('main');
    Route::get('/place-order', [KioskController::class, 'placeOrder'])->name('placeOrder');
    
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
    
// Profile routes
Route::middleware('auth')->group(function () {
    Route::get('/profile', [LoginController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [LoginController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [LoginController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';