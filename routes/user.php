<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\User\ShopController;
use App\Http\Controllers\User\CartController;
use App\Http\Controllers\User\CheckoutController;
use App\Livewire\User\OrderStatus;

Route::get('/', [ShopController::class, 'index'])->name('shop');

// Cart routes
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
// ... other cart routes

// Checkout
Route::get('/checkout', App\Livewire\User\Checkout::class)->name('checkout');

// Order Status
Route::get('/order-status/{order}', OrderStatus::class)->name('order.status');

// ... other user routes