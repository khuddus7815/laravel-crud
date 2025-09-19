<?php

use Illuminate\Support\Facades\Route;

// Controllers
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\Auth\CustomerLoginController;
use App\Http\Controllers\Auth\CustomerRegisterController; // Added this
use App\Http\Controllers\OrderController; // Added this

// Livewire Components
use App\Livewire\Items;
use App\Livewire\Coupons;
use App\Livewire\OrderStatus;
use App\Livewire\Admin\Orders as AdminOrders; // Using an alias to avoid naming conflicts if you have another 'Orders' class

// --- Public & Shop Routes ---

// Make the shop the new home page
Route::get('/', [ShopController::class, 'index'])->name('shop.index');
Route::get('/checkout', [ShopController::class, 'checkout'])->name('checkout');
Route::get('/order/{order:order_number}', [ShopController::class, 'success'])->name('order.success');


// --- Customer Authentication Routes ---
Route::get('customer/login', [CustomerLoginController::class, 'create'])->name('customer.login');
Route::post('customer/login', [CustomerLoginController::class, 'store']);
Route::post('customer/logout', [CustomerLoginController::class, 'destroy'])->name('customer.logout');
Route::get('customer/register', [CustomerRegisterController::class, 'create'])->name('customer.register'); // Assuming you have a 'create' method
Route::post('customer/register', [CustomerRegisterController::class, 'store']);


// --- Authenticated Customer Routes ---
Route::middleware('auth:customer')->group(function () {
    // This is the single, correct route for the customer's order status page
    Route::get('/my-orders', OrderStatus::class)->name('order.status');

    // These routes can stay if they handle specific actions from within the order status page
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
