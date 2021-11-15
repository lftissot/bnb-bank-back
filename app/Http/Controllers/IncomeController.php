<?php

namespace App\Http\Controllers;

use App\Enums\FileTypes;
use App\Enums\TransactionStatus;
use App\Enums\TransactionTypes;
use App\Http\Requests\ListIncomeRequest;
use App\Http\Requests\ListPendingIncomeRequest;
use App\Http\Requests\ListTransactionRequest;
use App\Http\Requests\Transaction\StoreIncomeRequest;
use App\Http\Requests\Transaction\UpdateTransactionRequest;
use App\Models\Transaction;
use App\Services\TransactionService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class IncomeController extends Controller
{
    public function list(ListTransactionRequest $request, TransactionService $transactionService) {
        $filters = $request->query();
        $incomes = $transactionService->getUserIncomes(auth('api')->user(), $filters);
        return response()->json($incomes);
    }

    public function store(StoreIncomeRequest $request, TransactionService $transactionService) {
        $validatedData = $request->validated();
        $savedFile = $request->file('file')->store('checks', ['disk'=>"public"]);

        $transaction = $transactionService->createIncomeForUser($validatedData, auth('api')->user());
        $transactionService->setIncomeImage($transaction, "storage/$savedFile");
                
        return response()->json($transaction);
    }

    public function listPending(ListPendingIncomeRequest $request, TransactionService $transactionService) {
        $filters = $request->query();
        $incomes = $transactionService->getAllPendingIncomes($filters);
        return response()->json($incomes);
    }

    public function update(UpdateTransactionRequest $request, TransactionService $transactionService, Transaction $transaction) {
        $transaction = $transactionService->updateTransaction($transaction, $request->validated());
        return response()->json($transaction);
    }
}
