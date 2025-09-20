<?php

use Illuminate\Support\Facades\Route;

// Controllers
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Auth\CustomerLoginController;
use App\Http\Controllers\Auth\CustomerRegisterController;

// Livewire Components - CORRECTED NAMESPACES
use App\Livewire\Admin\Items;
use App\Livewire\Admin\Coupons;
use App\Livewire\User\OrderStatus; // Corrected from App\Livewire\OrderStatus
use App\Livewire\Admin\Orders as AdminOrders;
use App\Livewire\User\Shop; // <-- Make sure this is imported

// --- Public & Shop Routes ---

// Make the shop the new home page
Route::get('/checkout', function () {
    return view('checkout');
})->name('checkout');


Route::get('/', Shop::class)->name('shop.index');


// --- Customer Authentication Routes ---
Route::get('customer/login', [CustomerLoginController::class, 'create'])->name('customer.login');
Route::post('customer/login', [CustomerLoginController::class, 'store']);
Route::post('customer/logout', [CustomerLoginController::class, 'destroy'])->name('customer.logout');
Route::get('customer/register', [CustomerRegisterController::class, 'create'])->name('customer.register');
Route::post('customer/register', [CustomerRegisterController::class, 'store']);


// --- Authenticated Customer Routes ---
Route::middleware('auth:customer')->group(function () {
    Route::get('/my-orders', OrderStatus::class)->name('order.status');
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