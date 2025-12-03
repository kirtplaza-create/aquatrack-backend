<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SaleController extends Controller
{
    /**
     * Display a listing of the sales.
     */
    public function index()
    {
        return Sale::orderBy('created_at', 'desc')->get();
    }

    /**
     * Store a newly created sale.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name'              => ['required', 'string', 'max:255'],
            'purpose'           => ['required', 'string', 'max:255'],
            'gallons'           => ['required', 'integer', 'min:1'],
            'price_per_gallon'  => ['required', 'numeric', 'min:0'],
            'total_amount'      => ['required', 'numeric', 'min:0'],
            'status'            => ['required', 'string', 'max:255'], // Collectables, Done
        ]);

        $sale = Sale::create($data);

        return response()->json($sale, 201);
    }

    /**
     * Display a specific sale.
     */
    public function show(Sale $sale)
    {
        return $sale;
    }

    /**
     * Update the specified sale.
     */
    public function update(Request $request, Sale $sale)
    {
        $data = $request->validate([
            'name'              => ['sometimes', 'required', 'string', 'max:255'],
            'purpose'           => ['sometimes', 'required', 'string', 'max:255'],
            'gallons'           => ['sometimes', 'required', 'integer', 'min:1'],
            'price_per_gallon'  => ['sometimes', 'required', 'numeric', 'min:0'],
            'total_amount'      => ['sometimes', 'required', 'numeric', 'min:0'],
            'status'            => ['sometimes', 'required', 'string', 'max:255'],
        ]);

        $sale->update($data);

        return $sale;
    }

    /**
     * Remove the specified sale.
     */
    public function destroy(Sale $sale)
    {
        $sale->delete();

        return response()->json(null, 204);
    }

    /**
     * Revenue statistics for the dashboard.
     *
     * GET /api/sales/stats?from=YYYY-MM-DD&to=YYYY-MM-DD
     * If from/to not provided, uses current month.
     */
    public function stats(Request $request)
    {
        $from = $request->query('from');
        $to   = $request->query('to');

        if (!$from || !$to) {
            $start = Carbon::now()->startOfMonth();
            $end   = Carbon::now()->endOfMonth();
        } else {
            $start = Carbon::parse($from)->startOfDay();
            $end   = Carbon::parse($to)->endOfDay();
        }

        // Raw daily sums for days that actually have Done sales
        $daily = DB::table('sales')
            ->selectRaw('DATE(created_at) as date, SUM(total_amount) as amount')
            ->whereBetween('created_at', [$start, $end])
            ->where('status', 'Done')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Reindex by date for quick lookup
        $byDate = $daily->keyBy('date');

        // Build continuous date range and fill missing days with amount = 0
        $filled = [];
        $cursor = $start->copy()->startOfDay();
        $last   = $end->copy()->startOfDay();

        while ($cursor->lte($last)) {
            $dateStr = $cursor->toDateString(); // YYYY-MM-DD

            if (isset($byDate[$dateStr])) {
                $filled[] = [
                    'date'   => $dateStr,
                    'amount' => (float) $byDate[$dateStr]->amount,
                ];
            } else {
                $filled[] = [
                    'date'   => $dateStr,
                    'amount' => 0.0,
                ];
            }

            $cursor->addDay();
        }

        $totalRevenue   = collect($filled)->sum('amount');
        $daysCount      = count($filled);
        $averageRevenue = $daysCount > 0 ? $totalRevenue / $daysCount : 0;

        return response()->json([
            'from'                   => $start->toDateString(),
            'to'                     => $end->toDateString(),
            'selected_period_revenue'=> $totalRevenue,
            'average_revenue'        => $averageRevenue,
            'total_revenue'          => $totalRevenue,
            'points'                 => $filled,
        ]);
    }
}
