<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\SaleController;
use App\Http\Controllers\Api\RefillController;
use App\Http\Controllers\Api\TransactionController;

// Login (fixed admin account from .env)
Route::post('/login', function (Request $request) {
    $data = $request->validate([
        'name'     => ['required', 'string'],
        'password' => ['required', 'string'],
    ]);

    $validName = env('APP_LOGIN_NAME', 'admin');
    $validPass = env('APP_LOGIN_PASSWORD', '1234');

    if ($data['name'] !== $validName || $data['password'] !== $validPass) {
        return response()->json(['message' => 'Invalid credentials'], 401);
    }

    $token = bin2hex(random_bytes(40));

    return response()->json([
        'user'  => ['name' => $validName],
        'token' => $token,
    ]);
});

// Protected routes
Route::middleware('simple.token')->group(function () {
    Route::apiResource('sales', SaleController::class);
    Route::apiResource('refills', RefillController::class);
    Route::apiResource('transactions', TransactionController::class);
});
