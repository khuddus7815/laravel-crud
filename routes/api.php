<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ItemController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\CouponController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// --- PUBLIC API ROUTES ---
Route::get('/items', [ItemController::class, 'index']); // This can remain public for browsing
Route::get('/categories', [CategoryController::class, 'index']); // This can also be public

// --- CUSTOMER-FACING API ROUTES (SESSION-BASED) ---
Route::middleware(['web', 'auth:customer'])->group(function () {
    Route::post('/orders', [OrderController::class, 'store']);
    Route::post('/apply-coupon', [CouponController::class, 'apply']);
});

// --- PROTECTED ROUTES ---
Route::middleware('auth:sanctum')->group(function () {
    // Get the currently authenticated user's details
    // This is typically for SPA/mobile apps using Sanctum tokens
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // Log the user out
    Route::post('/logout', [\App\Http\Controllers\Auth\AuthenticatedSessionController::class, 'destroy'])->name('api.logout');
});