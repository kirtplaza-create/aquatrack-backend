<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Sale; // if your sales model is named Sale

class SettingsController extends Controller
{
    public function prices() // GET /api/settings/prices
    {
        $regular = DB::table('settings')->where('key', 'price_regular')->value('value') ?? 15; // default 15
        $small   = DB::table('settings')->where('key', 'price_small')->value('value') ?? 10; // default 10

        return response()->json([ // return current prices
            'price_regular' => (float) $regular, // cast to float
            'price_small'   => (float) $small, // cast to float
        ]);
    }

    public function updatePrices(Request $request) // PUT /api/settings/prices
    {
        $data = $request->validate([
            'price_regular' => 'required|numeric|min:0',
            'price_small'   => 'required|numeric|min:0',
        ]);

        DB::table('settings')->updateOrInsert(
            ['key' => 'price_regular'],
            ['value' => $data['price_regular']]
        );

        DB::table('settings')->updateOrInsert(
            ['key' => 'price_small'],
            ['value' => $data['price_small']]
        );

        return response()->json(['message' => 'Settings updated']);
    }

    // NEW: dangerous system reset
    public function resetSystem(Request $request)
    {
        // 1) confirm admin password
        $request->validate([
            'password' => ['required', 'string'],
        ]);

        $user = User::first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Invalid admin password'], 403);
        }

        // 2) perform reset – adjust to your tables
        // example: delete all sales and maybe other data
        Sale::truncate();
        // DB::table('settings')->truncate(); // only if you want to clear settings too, otherwise remove

        return response()->json(['ok' => true]);
    }
}
