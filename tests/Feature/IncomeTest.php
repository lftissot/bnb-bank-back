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

class IncomeTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void {
        parent::setUp();
        $this->seed();
    }

    public function test_list_incomes_success() {
        $user = $this->createUserWithTransactions();

        $token = auth('api')->attempt(['username'=>$user->username, 'password'=>'1234']);

        $response = $this->get('/api/incomes');

        $response->assertStatus(200)->assertJsonStructure([
            "*" => [
            'id','description','amount','type','status','executed_on' 
            ]
        ])->assertJson(function (AssertableJson $json) {
            return $json->each(function ($json) {
                return $json->where('type', '1')
                    ->etc();
            });
        });
    }

    public function test_list_incomes_with_date()
    {
        $user = $this->createUserWithTransactions();

        $token = auth('api')->attempt(['username'=>$user->username, 'password'=>'1234']);

        $response = $this->get('/api/incomes?month=1&year=2021');

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

    public function test_list_incomes_with_status()
    {
        $user = $this->createUserWithTransactions();

        $token = auth('api')->attempt(['username'=>$user->username, 'password'=>'1234']);

        $response = $this->get('/api/incomes?status=0');

        $response->assertStatus(200)->assertJsonStructure([
            "*" => [
             'id','description','amount','type','status','executed_on' 
            ]
        ])->assertJson(function (AssertableJson $json) {
            return $json->each(function ($json) {
                return $json->where('status', '0')
                    ->etc();
            });
        });
    }

    public function test_list_incomes_with_date_fail()
    {
        $user = $this->createUserWithTransactions();

        $token = auth('api')->attempt(['username'=>$user->username, 'password'=>'1234']);

        $response = $this->get('/api/incomes?month=1&year=1960');

        $response->assertStatus(200)->assertJsonStructure([
            "*" => [
             'id','description','amount','type','status','executed_on' 
            ]
        ])->assertExactJson([]);
    }

    public function test_store_income_success() {
        Storage::fake('checks');

        $user = $this->createUserWithTransactions();

        $token = auth('api')->attempt(['username'=>$user->username, 'password'=>'1234']);

        $expenseData = [
            'amount'=>"10.5",
            'description'=>"Orange Juice",
            'file'=>UploadedFile::fake()->image("teste.png")
        ];

        $previousBalance = $user->refresh()->balance;
        
        $response = $this->post('/api/incomes', $expenseData);

        $response->assertStatus(200)->assertJsonStructure([
            'id','description','amount','type','status','executed_on' 
        ]);

        unset($expenseData['file']);
        $this->assertDatabaseHas('transactions', $expenseData);

        $currentBalance = $user->refresh()->balance;

        $this->assertTrue($previousBalance == $currentBalance);
    }

    public function test_list_pending_incomes_success() {
        $user = $this->createUserWithTransactions();
        $user->syncRoles([Role::findByName("Admin","api")]);

        $token = auth('api')->attempt(['username'=>$user->username, 'password'=>'1234']);

        $response = $this->getjson('/api/adm/pendingIncomes');

        $response->assertStatus(200)->assertJsonStructure([
            "*" => [
            'id','description','amount','type','status','executed_on' 
            ]
        ])->assertJson(function (AssertableJson $json) {
            return $json->each(function ($json) {
                return $json->where('type', '1')
                    ->where('status', '0')
                    ->etc();
            });
        });
    }

    public function test_approve_income_success() {
        $targetUser = User::factory()->createOne();
        $previousBalance = $targetUser->balance;
        
        $user = $this->createUserWithTransactions();
        $user->syncRoles([Role::findByName("Admin","api")]);

        $token = auth('api')->attempt(['username'=>$user->username, 'password'=>'1234']);

        $pendingTransaction = $targetUser->transactions()->create([
            'description' => 'Check',
            'amount' => 1.0,
            'type' => TransactionTypes::Check,
            'status' => TransactionStatus::Pending,
            'executed_on' => "2020-12-31 00:00:00",
        ]);

        $response = $this->patchJson('/api/adm/pendingIncomes/' . $pendingTransaction->id, ['status'=>TransactionStatus::Approved]);

        $response->assertOk();

        $pendingTransaction->refresh();
        $this->assertTrue($pendingTransaction->status == TransactionStatus::Approved);

        $currentBalance = $targetUser->refresh()->balance;
        $this->assertTrue($currentBalance == $previousBalance + 1);
    }

    public function test_denied_income_success() {
        $targetUser = User::factory()->createOne();
        $previousBalance = $targetUser->balance;

        $user = $this->createUserWithTransactions();
        $user->syncRoles([Role::findByName("Admin","api")]);

        $token = auth('api')->attempt(['username'=>$user->username, 'password'=>'1234']);

        $pendingTransaction = $targetUser->transactions()->create([
            'description' => 'Check',
            'amount' => 1.0,
            'type' => TransactionTypes::Check,
            'status' => TransactionStatus::Pending,
            'executed_on' => "2020-12-31 00:00:00",
        ]);

        $response = $this->patchJson('/api/adm/pendingIncomes/' . $pendingTransaction->id, ['status'=>TransactionStatus::Denied]);
        
        $response->assertOk();

        $pendingTransaction->refresh();
        $this->assertTrue($pendingTransaction->status == TransactionStatus::Denied);

        $currentBalance = $targetUser->refresh()->balance;
        $this->assertTrue($previousBalance == $currentBalance);
    }
}
