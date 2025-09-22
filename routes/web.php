<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Livewire\Admin\Items;
use App\Livewire\Admin\Coupons;
use App\Livewire\Admin\Orders as AdminOrders;
use App\Livewire\User\Shop;
use App\Http\Controllers\OrderStatusController;
use App\Livewire\User\Checkout;
use App\Livewire\SuperAdminDashboard;

// --- Public & Shop Routes ---
Route::get('/', Shop::class)->name('shop.index');

// --- Authenticated User Routes ---
Route::middleware('auth')->group(function () {
    Route::get('/checkout', Checkout::class)->name('checkout');
    // Corrected the typo below from Route. to Route::
    Route::get('/my-orders', OrderStatusController::class)->name('order.status');
});

// --- Admin Routes ---
Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/dashboard', function () {
        return redirect()->route('items');
    })->name('dashboard');

    Route::get('/items', Items::class)->name('items');
    Route::get('/coupons', Coupons::class)->name('coupons');
    Route::get('/orders', AdminOrders::class)->name('admin.orders');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// --- Super Admin Routes ---
Route::middleware(['auth', 'super.admin'])->group(function () {
    Route::get('/super-admin', SuperAdminDashboard::class)->name('super.admin.dashboard');
});

// --- Authentication Routes ---
require __DIR__.'/auth.php';
