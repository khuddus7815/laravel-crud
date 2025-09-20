<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ItemController;
use App\Http\Controllers\Admin\CategoryController;
// ... other admin controllers

Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Your API-style routes for managing resources
    Route::apiResource('items', ItemController::class);
    Route::apiResource('categories', CategoryController::class);
    // ... other admin routes
});