<?php
namespace App\Services;

use App\Models\User;
use Illuminate\Validation\ValidationException;

class UserService {

    public function __construct()
    {
        
    }

    public function create($user) : User {
        $user = User::create($user);
        return $user;
    }

    public function getToken($data) : string {
        $token = auth('api')->attempt($data);
        
        if(!$token)
            throw ValidationException::withMessages(["Invalid user or password"]);

        return $token;
    }

    public function getUserData(User $user) : array {
        return [
            'balance'=>round((float) $user->balance, 2),
            'incomes'=>round($user->approvedIncomes->sum('amount'), 2),
            'expenses'=>round($user->expenses->sum('amount'), 2),
        ];
    }

}