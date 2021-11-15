<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\IncomeController;
use App\Http\Controllers\TransactionController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('/auth/register', [AuthController::class, 'register']); // ok
Route::post('/auth/login', [AuthController::class, 'login']); // ok

Route::middleware(['auth:api'])->group(function () {
    Route::get('/me', [AuthController::class, 'me']); // ok

    Route::get('/transactions', [TransactionController::class, 'list']); // ok
    
    Route::get('/expenses', [ExpenseController::class, 'list']); // ok
    Route::post('/expenses', [ExpenseController::class, 'store']); // ok
    
    Route::get('/incomes', [IncomeController::class, 'list']); // ok
    Route::post('/incomes', [IncomeController::class, 'store']); // ok
    
    Route::prefix('/adm')->group(function () {
        Route::get('/pendingIncomes', [IncomeController::class, 'listPending']); // ok
        Route::patch('/pendingIncomes/{transaction}', [IncomeController::class, 'update']); // ok
    });
});