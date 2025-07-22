<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Auth\LoginController;
use Illuminate\Support\Facades\Route;

//remove me after testing
use Illuminate\Support\Facades\Hash;
use App\Models\User;

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

Route::get('/sales', function () {
    return view('profile.sales');
})->name('sales');

Route::get('/product', function () {
    return view('profile.product');
})->name('product');

// Admin dashboard with different view
Route::get('/admin/sip-serve-dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('admin.dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [LoginController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [LoginController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [LoginController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';