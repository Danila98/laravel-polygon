<?php

use App\Http\Controllers\Api\Accounting\AccountController;
use App\Http\Controllers\Api\Accounting\CategoryController;
use App\Http\Controllers\Api\Accounting\TransactionController;
use App\Http\Controllers\Api\AuthController;
use App\Models\Accounting\Transaction;
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

Route::group([
    'middleware' => 'api',
    'prefix' => 'auth'
], function ($router) {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
    Route::get('logout', [AuthController::class, 'logout']);
    Route::get('refresh', [AuthController::class, 'refresh']);
    Route::get('me', [AuthController::class, 'me']);
    Route::get('loginFail', [AuthController::class, 'loginFail'])->name('loginFail');
});
Route::group([
    'middleware' => 'api',
    'prefix' => 'category'
], function ($router) {
    Route::get('/', [CategoryController::class, 'list'])->middleware('auth:api');
    Route::post('/add', [CategoryController::class, 'store'])->middleware('auth:api');
    Route::put('/update/{id}', [CategoryController::class, 'update'])->middleware('auth:api');
    Route::delete('/delete/{id}', [CategoryController::class, 'destroy'])->middleware('auth:api');
    Route::get('/{id}', [CategoryController::class, 'show'])->middleware('auth:api');
});
Route::group([
    'middleware' => 'api',
    'prefix' => 'transaction'
], function ($router) {
    Route::get('/', [TransactionController::class, 'list'])->middleware('auth:api');
    Route::post('/add', [TransactionController::class, 'store'])->middleware('auth:api');
    Route::put('/update/{id}', [TransactionController::class, 'update'])->middleware('auth:api');
    Route::delete('/delete/{id}', [TransactionController::class, 'destroy'])->middleware('auth:api');
    Route::get('/{id}', [TransactionController::class, 'show'])->middleware('auth:api');
});

Route::group([
    'prefix' => 'account'
], function ($router) {
    Route::post('/store', [AccountController::class, 'setTotal']);
    Route::get('/{id}', [AccountController::class, 'getAccount']);

});
