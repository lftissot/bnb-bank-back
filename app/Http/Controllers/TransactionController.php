<?php

namespace App\Http\Controllers;

use App\Http\Requests\ListTransactionRequest;
use App\Http\Requests\Transaction\StoreTransactionRequest;
use App\Http\Requests\Transaction\UpdateTransactionRequest;
use App\Models\Transaction;
use App\Services\TransactionService;

class TransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function list(ListTransactionRequest $request, TransactionService $transactionService)
    {
        $filters = $request->query();
        $transactions = $transactionService->getUserApprovedTransactions(auth('api')->user(), $filters);
        return response()->json($transactions);
    }
}
