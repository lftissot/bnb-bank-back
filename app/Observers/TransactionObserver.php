<?php

namespace App\Observers;

use App\Models\Transaction;
use TransactionTypes;

class TransactionObserver
{
    public function saving(Transaction $transaction) {
        $transaction->checkConsolidation();
    }
}
