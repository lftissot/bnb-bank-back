<?php

namespace App\Http\Controllers;

use App\Enums\TransactionStatus;
use App\Enums\TransactionTypes;
use App\Http\Requests\ListExpenseRequest;
use App\Http\Requests\ListTransactionRequest;
use App\Http\Requests\Transaction\StoreExpenseRequest;
use App\Services\TransactionService;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    public function list(ListTransactionRequest $request, TransactionService $transactionService) {
        $filters = $request->query();
        $expenses = $transactionService->getUserExpenses(auth('api')->user(), $filters);
        return response()->json($expenses);
    }

    public function store(StoreExpenseRequest $request, TransactionService $transactionService) {
        $transaction = $transactionService->createExpenseForUser($request->validated(), auth()->user());
        return response()->json($transaction);
    }
}
