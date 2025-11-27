<?php

namespace App\Http\Controllers\Api;

use App\Models\Refill;
use Illuminate\Http\Request;

class RefillController
{
    // GET /api/refills
    public function index()
    {
        return Refill::with('sale')->get();
    }

    // POST /api/refills
    public function store(Request $request)
    {
        $data = $request->validate([
            'sale_id'          => 'nullable|exists:sales,id',
            'type'             => 'required|in:Walk-in,Delivery',
            'gallons_dispensed'=> 'required|integer|min:1',
            'completed_at'     => 'nullable|date',
        ]);

        $refill = Refill::create($data);

        return response()->json($refill, 201);
    }

    // GET /api/refills/{refill}
    public function show(Refill $refill)
    {
        return $refill->load('sale');
    }

    // PUT/PATCH /api/refills/{refill}
    public function update(Request $request, Refill $refill)
    {
        $data = $request->validate([
            'sale_id'          => 'sometimes|nullable|exists:sales,id',
            'type'             => 'sometimes|required|in:Walk-in,Delivery',
            'gallons_dispensed'=> 'sometimes|required|integer|min:1',
            'completed_at'     => 'nullable|date',
        ]);

        $refill->update($data);

        return response()->json($refill);
    }

    // DELETE /api/refills/{refill}
    public function destroy(Refill $refill)
    {
        $refill->delete();

        return response()->noContent();
    }
}
