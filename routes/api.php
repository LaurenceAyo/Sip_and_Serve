<?php
// routes/api.php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;

Route::middleware('auth:sanctum')->group(function () {
    // User management routes
    Route::get('users', [UserController::class, 'index']);
    Route::post('users', [UserController::class, 'store']);
    Route::get('users/{id}', [UserController::class, 'show']);
    Route::put('users/{id}', [UserController::class, 'update']);
    Route::delete('users/{id}', [UserController::class, 'destroy']);
    Route::post('users/{id}/reset-password', [UserController::class, 'resetPassword']);
});