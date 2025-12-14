<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Api\SaleController;
use App\Http\Controllers\Api\AccountController;
use App\Http\Controllers\Api\SettingsController;
use App\Models\User;

// Login (uses users table now)
Route::post('/login', function (Request $request) {
    $data = $request->validate([
        'name'     => ['required', 'string'],
        'password' => ['required', 'string'],
    ]);

    // for now assume single admin row
    $user = User::first();

    if (! $user || $data['name'] !== $user->name || ! Hash::check($data['password'], $user->password)) {
        return response()->json(['message' => 'Invalid credentials'], 401);
    }

    // simple random token, stored on frontend (not Sanctum)
    $token = bin2hex(random_bytes(40));

    return response()->json([
        'user'  => ['name' => $user->name],
        'token' => $token,
    ]);
});

// Protected routes
Route::middleware('simple.token')->group(function () {
    // Extra protection: confirm admin password before dangerous actions like delete
    Route::post('/admin/confirm-password', function (Request $request) {
        $request->validate([
            'password' => ['required', 'string'],
        ]);

        $user = User::first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Invalid admin password'], 403);
        }

        return response()->json(['ok' => true]);
    });

    // Account info
    Route::get('/account', [AccountController::class, 'show']);
    Route::put('/account', [AccountController::class, 'update']);

    // Sales routes
    Route::get('/sales/stats', [SaleController::class, 'stats']);
    Route::get('/sales/year-stats', [SaleController::class, 'yearStats']);
    Route::get('/sales/today', [SaleController::class, 'today']);
    Route::apiResource('sales', SaleController::class);
    Route::get('/debug-now', fn () => now()->toDateTimeString());

    // Settings routes
    Route::get('/settings/prices', [SettingsController::class, 'prices']);
    Route::put('/settings/prices', [SettingsController::class, 'updatePrices']);

    // Dangerous system reset
    Route::post('/settings/reset-system', [SettingsController::class, 'resetSystem']);
});
