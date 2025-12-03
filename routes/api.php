<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\SaleController;

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

    // simple random token, stored on frontend (not Sanctum)
    $token = bin2hex(random_bytes(40));

    return response()->json([
        'user'  => ['name' => $validName],
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

        $validPass = env('APP_LOGIN_PASSWORD', '1234');

        if ($request->password !== $validPass) {
            return response()->json(['message' => 'Invalid admin password'], 403);
        }

        return response()->json(['ok' => true]);
    });

    // IMPORTANT: stats before apiResource so it is not treated as {sale}
    Route::get('/sales/stats', [SaleController::class, 'stats']);

    Route::apiResource('sales', SaleController::class);
});
