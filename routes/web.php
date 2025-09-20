<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Livewire\Admin\Items;
use App\Livewire\Admin\Coupons;
use App\Livewire\Admin\Orders as AdminOrders;
use App\Livewire\User\Shop;
use App\Livewire\User\OrderStatus;
use App\Http\Controllers\OrderStatusController;

// --- Public & Shop Routes ---
Route::get('/', Shop::class)->name('shop.index');
Route::get('/checkout', function () {
    return view('checkout');
})->name('checkout');

// --- Authenticated User Routes ---
Route::middleware('auth')->group(function () {
    // === THIS IS THE KEY CHANGE ===
    // Use the new controller instead of the Livewire component
    Route::get('/my-orders', OrderStatusController::class)->name('order.status');
    // =============================
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

require __DIR__.'/auth.php';