<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void {
        parent::setUp();
        $this->seed();
    }

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_register_success()
    {
        $data = [
            "username"=>"Luís Felipe Tissot",
            "email"=>"lftissot@gmail.com",
            "password"=>"1234", // safety first
        ];
        
        $response = $this->postJson('/api/auth/register', $data);

        $response->assertOk()
            ->assertJsonStructure([
                'id','username','email'
            ]);

        unset($data['password']);
        $this->assertDatabaseHas('users', $data);
    }

    public function test_register_validations()
    {
        $data = [
            "username"=>"",
            "email"=>"",
            "password"=>"", // safety first
        ];
        
        $response = $this->postJson('/api/auth/register', $data);

        $response->assertJsonValidationErrors(['username','email','password']);
    }

    public function test_login_success() {
        $user = User::factory()->createOne();

        $loginData = [
            'username'=>$user->username,
            'password'=>'1234',
        ];

        $response = $this->postJson('/api/auth/login', $loginData);

        $response->assertOk()->assertJsonStructure([
            'token'
        ]);
    }

    public function test_login_failure() {
        $user = User::factory()->createOne();

        $loginData = [
            'username'=>$user->username,
            'password'=>'4567',
        ];

        $response = $this->postJson('/api/auth/login', $loginData);

        $response->assertStatus(422);
    }

    public function test_unauthenticated_request() {
        $response = $this->getJson('/api/transactions', []);
        $response->assertUnauthorized();
    }

    public function test_get_data() {
        $user = $this->createUserWithTransactions();

        $token = auth('api')->attempt(['username'=>$user->username, 'password'=>'1234']);

        $response = $this->get('/api/me/');

        $response->assertStatus(200)->assertJsonStructure([
             'balance','incomes','expenses' 
        ]);
    }
}
