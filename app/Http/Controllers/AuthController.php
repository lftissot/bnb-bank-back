<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\User\StoreUserRequest;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public $userService;
    
    public function __construct()
    {
        $this->userService = new UserService();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\User\StoreUserRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function register(StoreUserRequest $request)
    {
        $userData = $request->all();
        $user = $this->userService->create($userData);
        return response()->json($user, 200);
    }

    public function login(LoginRequest $request) {
        $token = $this->userService->getToken($request->validated());
        return response()->json(['token'=>$token]);
    }

    public function me(Request $r) {
        $user = auth()->user();
        $data = $this->userService->getUserData($user);
        return response()->json($data);
    }
}
