<?php

namespace Tests;

use App\Enums\TransactionStatus;
use App\Enums\TransactionTypes;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected function createUserWithTransactions() {
        return User::factory()->has(Transaction::factory()->state(new Sequence(
            ['type' => TransactionTypes::Check, 'status'=>TransactionStatus::Pending],
            ['type' => TransactionTypes::Check, 'status'=>TransactionStatus::Approved, 'executed_on'=>"2021-01-01 00:00:00"],
            ['type' => TransactionTypes::Check, 'status'=>TransactionStatus::Denied,  'executed_on'=>"2020-01-01 00:00:00"],
            ['type' => TransactionTypes::ManualExpense, 'status'=>TransactionStatus::Approved, 'executed_on'=>"2021-01-01 00:00:00"],
            ['type' => TransactionTypes::ManualExpense, 'status'=>TransactionStatus::Approved, 'executed_on'=>"2020-01-01 00:00:00"],
        ))->count(8))->createOne();
    }
}
