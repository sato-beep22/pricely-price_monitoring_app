<?php

use App\Http\Controllers\Admin\PriceImportController;
use App\Http\Controllers\Admin\SmsLogController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\Buyer\SmsNotificationController;
use App\Http\Controllers\CeilingPriceController;
use App\Http\Controllers\ChatbotController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ForecastController;
use App\Http\Controllers\MapController;
use App\Http\Controllers\PhoneVerificationController;
use App\Http\Controllers\PriceController;
use App\Http\Controllers\PriceTrendController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\SubscriptionController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Web-based cron job endpoint for shared hosting
Route::get('/run-queue', function (Request $request) {
    // Basic security token to prevent random visitors from triggering it
    if ($request->query('token') !== 'pricely123') {
        abort(403, 'Unauthorized');
    }

    Artisan::call('queue:work', ['--stop-when-empty' => true]);

    return response()->json(['status' => 'Queue processed successfully']);
});

// Helper to clear caches on shared hosting
Route::get('/clear-cache', function (Request $request) {
    if ($request->query('token') !== 'pricely123') {
        abort(403, 'Unauthorized');
    }

    Artisan::call('optimize:clear');
    
    return response()->json(['status' => 'All caches cleared successfully']);
});

// Helper to create the storage symlink on shared hosting (needed for file uploads)
Route::get('/storage-link', function (Request $request) {
    if ($request->query('token') !== 'pricely123') {
        abort(403, 'Unauthorized');
    }

    Artisan::call('storage:link');

    return response()->json(['status' => 'Storage linked successfully']);
});

// Helper to view logs securely
Route::get('/debug-log', function (Request $request) {
    if ($request->query('token') !== 'pricely123') {
        abort(403, 'Unauthorized');
    }
    $logPath = storage_path('logs/laravel.log');
    if (!file_exists($logPath)) {
        return response('No log file found.', 404);
    }
    // Get last 100 lines
    $lines = file($logPath);
    $lastLines = array_slice($lines, -100);
    return response('<pre>' . implode('', $lastLines) . '</pre>')->header('Content-Type', 'text/html');
});

// Public map access
Route::get('/map', [MapController::class, 'index'])->name('map.index');

Route::get('/language/{locale}', function ($locale) {
    if (! in_array($locale, ['en', 'tl'])) {
        abort(400);
    }
    session()->put('locale', $locale);

    return back();
})->name('language.switch');

Route::middleware('auth')->group(function () {
    // Shared Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Pricely AI Chatbot
    Route::post('/chatbot', [ChatbotController::class, 'chat'])->name('chatbot.chat');

    // Shared Profile (from Breeze)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::put('/profile/pin', [ProfileController::class, 'updatePin'])->name('profile.pin.update');

    Route::post('/onboarding/complete', function () {
        auth()->user()->update(['has_seen_tour' => true]);

        return response()->json(['status' => 'success']);
    })->name('onboarding.complete');

    Route::get('/api/price-trend', [PriceTrendController::class, 'index'])->name('api.price-trend');

    // Forecasting (Farmers & Admins usually, but available to all auth users)
    Route::get('/forecast', [ForecastController::class, 'index'])->name('forecast.index');

    // --- FARMER ROUTES ---
    Route::middleware('role:farmer')->group(function () {
        Route::post('/profile/phone/send', [PhoneVerificationController::class, 'store'])->name('phone.verification.send');
        Route::post('/profile/phone/verify', [PhoneVerificationController::class, 'verify'])->name('phone.verification.verify');

        Route::get('/subscriptions', [SubscriptionController::class, 'index'])->name('subscriptions.index');
        Route::post('/subscriptions', [SubscriptionController::class, 'store'])->middleware('phone.verified')->name('subscriptions.store');
        Route::patch('/subscriptions/{subscription}', [SubscriptionController::class, 'update'])->name('subscriptions.update');
        Route::delete('/subscriptions/{subscription}', [SubscriptionController::class, 'destroy'])->name('subscriptions.destroy');
    });

    // --- BUYER ROUTES ---
    Route::middleware('role:buyer')->group(function () {
        Route::post('/profile/buyer/phone/send', [PhoneVerificationController::class, 'store'])->name('buyer.phone.verification.send');
        Route::post('/profile/buyer/phone/verify', [PhoneVerificationController::class, 'verify'])->name('buyer.phone.verification.verify');
        Route::patch('/profile/sms-notifications', [SmsNotificationController::class, 'toggle'])->name('buyer.sms-notifications.toggle');

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
        Route::delete('/users/{user}', [AdminUserController::class, 'destroy'])->name('users.destroy');

        Route::get('/ceiling-prices', [CeilingPriceController::class, 'index'])->name('ceiling-prices.index');
        Route::post('/ceiling-prices', [CeilingPriceController::class, 'store'])->name('ceiling-prices.store');

        Route::get('/price-import', [PriceImportController::class, 'index'])->name('price-import.index');
        Route::post('/price-import', [PriceImportController::class, 'store'])->name('price-import.store');
        Route::post('/price-import/source-link', [PriceImportController::class, 'updateSourceLink'])->name('price-import.source-link');

        // Reports (Now restricted to Admins)
        Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
        Route::get('/reports/export', [ReportController::class, 'export'])->name('reports.export');

        // SMS Logs
        Route::get('/sms-logs', [SmsLogController::class, 'index'])->name('sms-logs.index');
        Route::delete('/sms-logs/{smsLog}', [SmsLogController::class, 'destroy'])->name('sms-logs.destroy');
    });
});

require __DIR__.'/auth.php';
