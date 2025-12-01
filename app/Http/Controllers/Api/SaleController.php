<?php

namespace App\Http\Controllers\Api;

use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
class SaleController
{
    // GET /api/sales
    public function index()
    {
        return Sale::all();
    }

    // GET /api/sales/stats
    public function stats()
    {
        $totalRevenue = Sale::sum('total_amount');
        $todayRevenue = Sale::whereDate('created_at', Carbon::today())
            ->sum('total_amount');

        $totalGallonsSold = Sale::sum('gallons');
        $todayGallonsSold = Sale::whereDate('created_at', Carbon::today())
            ->sum('gallons');

        return response()->json([
            'total_revenue'       => $totalRevenue,
            'today_revenue'       => $todayRevenue,
            'total_gallons_sold'  => $totalGallonsSold,
            'today_gallons_sold'  => $todayGallonsSold,
        ]);
    }

    // POST /api/sales
    public function store(Request $request)
    {
        $data = $request->validate([
            'name'              => 'required|string|max:255',
            'purpose'           => 'required|in:Walk-in,Delivery',
            'gallons'           => 'required|integer|min:1',
            'price_per_gallon'  => 'required|numeric|min:0',
            'total_amount'      => 'required|numeric|min:0',
            'status'            => 'required|in:Collectables,Done',
        ]);

        $sale = Sale::create($data);

        return response()->json($sale, 201);
    }

    // GET /api/sales/{sale}
    public function show(Sale $sale)
    {
        return $sale;
    }

    // PUT/PATCH /api/sales/{sale}
    public function update(Request $request, Sale $sale)
    {
        $data = $request->validate([
            'name'              => 'sometimes|required|string|max:255',
            'purpose'           => 'sometimes|required|in:Walk-in,Delivery',
            'gallons'           => 'sometimes|required|integer|min:1',
            'price_per_gallon'  => 'sometimes|required|numeric|min:0',
            'total_amount'      => 'sometimes|required|numeric|min:0',
            'status'            => 'sometimes|required|in:Collectables,Done',
        ]);

        $sale->update($data);

        return response()->json($sale);
    }

    // DELETE /api/sales/{sale}
    public function destroy(Sale $sale)
    {
        $sale->delete();

        return response()->noContent();
    }
}
