<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Handle login for Aquatrack (single admin for now).
     */
    public function login(Request $request) // POST /api/login
    {
        $data = $request->validate([
            'name'     => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        // For now assume a single admin row
        $user = User::first();

        if (! $user || $data['name'] !== $user->name || ! Hash::check($data['password'], $user->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        // Simple random token, stored on frontend (not Sanctum)
        $token = bin2hex(random_bytes(40));

        return response()->json([
            'user'  => [
                'name' => $user->name,
                // 'role' => $user->role ?? 'admin', // uncomment when you add roles
            ],
            'token' => $token,
        ]);
    }
}
