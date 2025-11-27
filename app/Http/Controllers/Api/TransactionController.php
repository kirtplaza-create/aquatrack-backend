<?php

namespace App\Http\Controllers\Api;

use App\Models\Transaction;
use Illuminate\Http\Request;

class TransactionController
{
    // GET /api/transactions
    public function index()
    {
        return Transaction::with('sale')->get();
    }

    // POST /api/transactions
    public function store(Request $request)
    {
        $data = $request->validate([
            'sale_id' => 'required|exists:sales,id',
            'amount'  => 'required|numeric|min:0',
            'status'  => 'required|in:Collectables,Done',
        ]);

        $transaction = Transaction::create($data);

        return response()->json($transaction, 201);
    }

    // GET /api/transactions/{transaction}
    public function show(Transaction $transaction)
    {
        return $transaction->load('sale');
    }

    // PUT/PATCH /api/transactions/{transaction}
    public function update(Request $request, Transaction $transaction)
    {
        $data = $request->validate([
            'sale_id' => 'sometimes|required|exists:sales,id',
            'amount'  => 'sometimes|required|numeric|min:0',
            'status'  => 'sometimes|required|in:Collectables,Done',
        ]);

        $transaction->update($data);

        return response()->json($transaction);
    }

    // DELETE /api/transactions/{transaction}
    public function destroy(Transaction $transaction)
    {
        $transaction->delete();

        return response()->noContent();
    }
}
