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

class TransactionTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void {
        parent::setUp();
        $this->seed();
    }

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_list_transactions_success()
    {
        $user = $this->createUserWithTransactions();

        $token = auth('api')->attempt(['username'=>$user->username, 'password'=>'1234']);

        $response = $this->get('/api/transactions');

        $response->assertStatus(200)->assertJsonStructure([
            "*" => [
             'id','description','amount','type','status','executed_on' 
            ]
        ]);
    }

    public function test_list_transactions_with_date()
    {
        $user = $this->createUserWithTransactions();

        $token = auth('api')->attempt(['username'=>$user->username, 'password'=>'1234']);

        $response = $this->get('/api/transactions?month=1&year=2021');

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

    public function test_list_transactions_with_date_fail()
    {
        $user = $this->createUserWithTransactions();

        $token = auth('api')->attempt(['username'=>$user->username, 'password'=>'1234']);

        $response = $this->get('/api/transactions?month=1&year=1960');

        $response->assertStatus(200)->assertJsonStructure([
            "*" => [
             'id','description','amount','type','status','executed_on' 
            ]
        ])->assertExactJson([]);
    }
}
