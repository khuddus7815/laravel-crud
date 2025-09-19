<?php

use Illuminate\Support\Facades\Route;

// Controllers
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\Auth\CustomerLoginController;
use App\Http\Controllers\Auth\CustomerRegisterController;
use App\Http\Controllers\OrderController;
use App\Livewire\User\Checkout;
use App\Http\Controllers\CartController;
use App\Http\Controllers\Api\OrderController as ApiOrderController;

// Livewire Components - CORRECTED NAMESPACES
use App\Livewire\Admin\Items;
use App\Livewire\Admin\Coupons;
use App\Livewire\User\OrderStatus; // Corrected from App\Livewire\OrderStatus
use App\Livewire\Admin\Orders as AdminOrders;

// --- Public & Shop Routes ---

// Make the shop the new home page
Route::get('/', [ShopController::class, 'index'])->name('shop.index');
Route::get('/checkout', Checkout::class)->name('checkout'); 
Route::apiResource('orders', OrderController::class);
Route::post('/cart/update', [CartController::class, 'update'])->name('cart.update');


Route::get('/order/{order:order_number}', [ShopController::class, 'success'])->name('order.success');


// --- Customer Authentication Routes ---
Route::get('customer/login', [CustomerLoginController::class, 'create'])->name('customer.login');
Route::post('customer/login', [CustomerLoginController::class, 'store']);
Route::post('customer/logout', [CustomerLoginController::class, 'destroy'])->name('customer.logout');
Route::get('customer/register', [CustomerRegisterController::class, 'create'])->name('customer.register');
Route::post('customer/register', [CustomerRegisterController::class, 'store']);


// --- Authenticated Customer Routes ---
Route::middleware('auth:customer')->group(function () {
    Route::get('/my-orders', OrderStatus::class)->name('order.status');
    Route::patch('/orders/{order}/cancel', [OrderController::class, 'cancel'])->name('order.cancel');
    Route::patch('/orders/{order}/return', [OrderController::class, 'return'])->name('order.return');
});


// --- Admin Routes ---

// Redirect /dashboard to the /items route
Route::get('/dashboard', function () {
    return redirect()->route('items');
})->middleware(['auth', 'verified'])->name('dashboard');

// Admin-only routes (uses the default 'web' auth guard)
Route::middleware('auth')->group(function () {
    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Livewire Admin Panels
    Route::get('/items', Items::class)->name('items');
    Route::get('/coupons', Coupons::class)->name('coupons');
    Route::get('/orders', AdminOrders::class)->name('admin.orders');
});


// This file handles the default ADMIN login/register routes provided by Laravel Breeze/UI
require __DIR__.'/auth.php';