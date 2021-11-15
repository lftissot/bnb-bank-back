<?php

namespace Tests\Feature;

use App\Enums\TransactionStatus;
use App\Enums\TransactionTypes;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Testing\Fluent\AssertableJson;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ExpenseTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void {
        parent::setUp();
        $this->seed();
    }

    public function test_list_expenses_success() {
        $user = $this->createUserWithTransactions();

        $token = auth('api')->attempt(['username'=>$user->username, 'password'=>'1234']);

        $response = $this->get('/api/expenses');

        $response->assertStatus(200)->assertJsonStructure([
            "*" => [
            'id','description','amount','type','status','executed_on' 
            ]
        ])->assertJson(function (AssertableJson $json) {
            return $json->each(function ($json) {
                return $json->where('type', '0')
                    ->etc();
            });
        });
    }
    
    public function test_list_expenses_with_date()
    {
        $user = $this->createUserWithTransactions();

        $token = auth('api')->attempt(['username'=>$user->username, 'password'=>'1234']);

        $response = $this->get('/api/expenses?month=1&year=2021');

        $response->assertStatus(200)->assertJsonStructure([
            "*" => [
             'id','description','amount','type','status','executed_on' 
            ]
        ])->assertJson(function (AssertableJson $json) {
            return $json->each(function ($json) {
                return $json->where('executed_on', fn ($executed_on) => strpos($executed_on, "2021-01") === 0 )
                    ->where('executed_on', fn ($executed_on) => strpos($executed_on, "2020-01") === false )
                    ->etc();
            });
        });
    }

    public function test_list_expenses_with_date_fail()
    {
        $user = $this->createUserWithTransactions();

        $token = auth('api')->attempt(['username'=>$user->username, 'password'=>'1234']);

        $response = $this->get('/api/expenses?month=1&year=1960');

        $response->assertStatus(200)->assertJsonStructure([
            "*" => [
             'id','description','amount','type','status','executed_on' 
            ]
        ])->assertExactJson([]);
    }

    public function test_store_expense_success() {
        $user = $this->createUserWithTransactions();

        $token = auth('api')->attempt(['username'=>$user->username, 'password'=>'1234']);

        $expenseData = [
            'amount'=>-10.0,
            'description'=>"Orange Juice",
            'executed_on'=>"2020-12-31 00:00:01"
        ];

        $previousBalance = $user->refresh()->balance;
        
        $response = $this->postJson('/api/expenses', $expenseData);

        $response->assertStatus(200)->assertJsonStructure([
            'id','description','amount','type','status','executed_on' 
        ]);

        $this->assertDatabaseHas('transactions', $expenseData);

        $currentBalance = $user->refresh()->balance;

        $this->assertTrue($previousBalance - $currentBalance == 10);
    }

    public function test_unsuficient_funds() {
        $user = User::factory()->createOne();

        $token = auth('api')->attempt(['username'=>$user->username, 'password'=>'1234']);

        $expenseData = [
            'amount'=>($user->balance + 100)*-1,
            'description'=>"Orange Juice Machine",
            'executed_on'=>"2020-12-31 00:00:01"
        ];

        $response = $this->postJson('/api/expenses', $expenseData);

        $response->assertJsonValidationErrors([0]);
    }
}
