<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminSecurityController extends Controller
{
    /**
     * Confirm admin password before dangerous actions (e.g., delete).
     */
    public function confirmPassword(Request $request)
    {
        $request->validate([
            'password' => ['required', 'string'],
        ]);

        $user = User::first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Invalid admin password'], 403);
        }

        return response()->json(['ok' => true]);
    }
}
