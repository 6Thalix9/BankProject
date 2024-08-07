<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\RequestController;
use App\Http\Controllers\TransitionController;
use App\Http\Controllers\TestController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware(['basic.authentication'])->group(function () {
        Route::get('/accounts', [AccountController::class, 'index']);
        Route::post('/accounts', [AccountController::class, 'store']);
        Route::get('/accounts/{id}', [AccountController::class, 'show']);
        Route::get('/accounts/update/{id}', [AccountController::class, 'update']);
        Route::get('/accounts/delete/{id}', [AccountController::class, 'destroy']);
        Route::get('/accounts/{id}/transitions', [AccountController::class, 'transactionHistory']);

        
        Route::get('/transitions', [TransitionController::class, 'index']);
        Route::post('/transitions/{id}/withdraw', [TransitionController::class, 'withdraw']);
        Route::post('/transitions/{id}/deposit', [TransitionController::class, 'deposit']);

        Route::get('/users', [UserController::class, 'index']);
        Route::post('/users', [UserController::class, 'store']);
        
        Route::get('/users/update/{id}', [UserController::class, 'update']);
        Route::get('/users/delete/{id}', [UserController::class, 'destroy']);
        Route::get('/users/search', [UserController::class, 'search']);

    });

Route::post('/transitions/{fromAccount}/transfer/{toAccount}', [TransitionController::class, 'transfer']);
Route::get('/test/analytics', [TestController::class, 'analyticValues']);
Route::get('/users/{id}', [UserController::class, 'show']);

Route::get('/phpinfo', function () {
    return phpinfo();
});
Route::get('/request', [RequestController::class, 'request1']);