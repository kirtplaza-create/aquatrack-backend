<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AccountController extends Controller
{
    public function show(Request $request)
    {
        // single admin user for now
        $user = User::first();

        return response()->json([
            'name'    => $user?->name ?? env('APP_LOGIN_NAME', 'admin'),
            'phone'   => $user?->phone ?? null,
            'email'   => $user?->email ?? null,
            'address' => $user?->address ?? null,
        ]);
    }

   public function update(Request $request)
    {
        $data = $request->validate([
            'name'            => 'required|string|max:255',
            'phone'           => 'nullable|string|max:50',
            'email'           => 'required|email|max:255',
            'address'         => 'nullable|string|max:255',
            'currentPassword' => 'nullable|string',
            'newPassword'     => [
            'nullable',
            'string',
            'min:8',
            'regex:/[A-Z]/',        // at least one uppercase
            'regex:/[!@#$%^&*]/',   // at least one special char
        ],
    ]);


        $user = User::first() ?? new User();

        $user->name    = $data['name'];
        $user->phone   = $data['phone'] ?? null;
        $user->email   = $data['email'];
        $user->address = $data['address'] ?? null;

        // change password only if newPassword is provided
        if (!empty($data['newPassword'])) {
            // require correct current password
            if (empty($data['currentPassword']) || ! Hash::check($data['currentPassword'], $user->password)) {
                return response()->json(['message' => 'Current password is incorrect'], 422);
            }
            $user->password = Hash::make($data['newPassword']);
        }

        $user->save();

        return response()->json([
            'name'    => $user->name,
            'phone'   => $user->phone,
            'email'   => $user->email,
            'address' => $user->address,
        ]);
    }
}
