<?php
namespace App\Services;

use App\Enums\FileTypes;
use App\Enums\TransactionStatus;
use App\Enums\TransactionTypes;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

class TransactionService {

    public function __construct()
    {
        
    }

    public function createExpenseForUser(array $expense, User $user) : Transaction {
        $expense['status'] = TransactionStatus::Approved;
        $expense['type'] = TransactionTypes::ManualExpense;
        $expense['executed_on'] = Carbon::parse($expense['executed_on']);

        $transaction = $user->transactions()->create($expense);

        return $transaction;
    }

    public function createIncomeForUser(array $income, User $user) : Transaction {
        $income['status'] = TransactionStatus::Pending;
        $income['type'] = TransactionTypes::Check;
        $income['executed_on'] = Carbon::now();

        $transaction = $user->incomes()->create($income);

        return $transaction;
    }

    public function setIncomeImage(Transaction $transaction, string $file) : void {
        $transaction->file()->create([
            'path' => $file,
            'type' => FileTypes::Check
        ]);
    }

    public function getAllPendingIncomes(array $filters = []) : Collection {
        $query = Transaction::where('status', TransactionStatus::Pending)
            ->where('type', TransactionTypes::Check)
            ->with('check')
            ->with('user');

        $query = $this->applyTransactionFiltersToQuery($filters, $query);
        return $query->get();
    }

    public function updateTransaction(Transaction $transaction, array $data) : Transaction {
        $transaction->fill($data);
        $transaction->save();
        return $transaction;
    }

    public function getUserApprovedTransactions(User $user, $filters = []) : Collection {
        $query = $user->approvedTransactions();
        $query = $this->applyTransactionFiltersToQuery($filters, $query);
        return $query->get();
    }

    public function getUserIncomes(User $user, $filters = []) : Collection {
        $query = $user->incomes();
        $query = $this->applyTransactionFiltersToQuery($filters, $query);
        return $query->get();
    }

    public function getUserExpenses(User $user, $filters = []) : Collection {
        $query = $user->expenses();
        $query = $this->applyTransactionFiltersToQuery($filters, $query);
        return $query->get();
    }

    public function applyTransactionFiltersToQuery($filters, $query) {
        $query = $query->when(isset($filters['month']), function ($q) use ($filters) {
            $q->whereMonth('executed_on', $filters['month']);
        });

        $query = $query->when(isset($filters['year']), function ($q) use ($filters) {
            $q->whereYear('executed_on', $filters['year']);
        });

        $query = $query->when(isset($filters['status']), function ($q) use ($filters) {
            $q->where('status', $filters['status']);
        });

        return $query;
    }

}