<?php

use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\CeilingPriceController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ForecastController;
use App\Http\Controllers\MapController;
use App\Http\Controllers\PhoneVerificationController;
use App\Http\Controllers\PriceController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\SubscriptionController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Public map access
Route::get('/map', [MapController::class, 'index'])->name('map.index');

Route::middleware('auth')->group(function () {
    // Shared Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Shared Profile (from Breeze)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Forecasting (Farmers & Admins usually, but available to all auth users)
    Route::get('/forecast', [ForecastController::class, 'index'])->name('forecast.index');

    // Reports (Available to all auth users, mostly used by Farmers & Admins)
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/export', [ReportController::class, 'export'])->name('reports.export');

    // --- FARMER ROUTES ---
    Route::middleware('role:farmer')->group(function () {
        Route::post('/profile/phone/send', [PhoneVerificationController::class, 'store'])->name('phone.verification.send');
        Route::post('/profile/phone/verify', [PhoneVerificationController::class, 'verify'])->name('phone.verification.verify');

        Route::get('/subscriptions', [SubscriptionController::class, 'index'])->name('subscriptions.index');
        Route::post('/subscriptions', [SubscriptionController::class, 'store'])->middleware('phone.verified')->name('subscriptions.store');
        Route::delete('/subscriptions/{subscription}', [SubscriptionController::class, 'destroy'])->name('subscriptions.destroy');
    });

    // --- BUYER ROUTES ---
    Route::middleware('role:buyer')->group(function () {
        Route::get('/shop', [ShopController::class, 'show'])->name('shops.show');
        Route::get('/shop/edit', [ShopController::class, 'edit'])->name('shops.edit');
        Route::put('/shop', [ShopController::class, 'update'])->name('shops.update');

        Route::get('/prices/record', [PriceController::class, 'create'])->name('prices.create');
        Route::post('/prices', [PriceController::class, 'store'])->name('prices.store');
    });

    // --- ADMIN ROUTES ---
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
        Route::put('/users/{user}', [AdminUserController::class, 'update'])->name('users.update');

        Route::get('/ceiling-prices', [CeilingPriceController::class, 'index'])->name('ceiling-prices.index');
        Route::post('/ceiling-prices', [CeilingPriceController::class, 'store'])->name('ceiling-prices.store');

    });
});

require __DIR__.'/auth.php';
