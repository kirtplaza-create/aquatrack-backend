<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Api\SaleController;
use App\Http\Controllers\Api\AccountController;
use App\Http\Controllers\Api\SettingsController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\AdminSecurityController;

// Login (uses AuthController now)
Route::post('/login', [AuthController::class, 'login']);

// Protected routes
Route::middleware('simple.token')->group(function () {
    Route::post(
        '/admin/confirm-password',
        [AdminSecurityController::class, 'confirmPassword']
    );
    
    // Account info
    Route::get('/account', [AccountController::class, 'show']);
    Route::put('/account', [AccountController::class, 'update']);

    // Sales routes
    Route::get('/sales/stats', [SaleController::class, 'stats']);
    Route::get('/sales/year-stats', [SaleController::class, 'yearStats']);
    Route::get('/sales/today', [SaleController::class, 'today']);
    Route::get('/debug-now', fn () => now()->toDateTimeString());
    Route::apiResource('sales', SaleController::class);

    // Settings routes
    Route::get('/settings/prices', [SettingsController::class, 'prices']);
    Route::put('/settings/prices', [SettingsController::class, 'updatePrices']);

    // Dangerous system reset
    Route::post('/settings/reset-system', [SettingsController::class, 'resetSystem']);
});
