<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\Randomizer\ColorController;
use App\Http\Controllers\Api\Randomizer\TagController;
use App\Http\Controllers\Api\Shop\CatalogController;
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
    'prefix' => 'catalog'
], function ($router) {
    Route::get('/', [CatalogController::class, 'list'])->middleware('auth:api');
    Route::get('/product/{id}', [CatalogController::class, 'show'])->middleware('auth:api');
    Route::post('/add', [CatalogController::class, 'store'])->middleware('auth:api');
    Route::put('/update/{id}', [CatalogController::class, 'update'])->middleware('auth:api');
    Route::delete('/delete/{id}', [CatalogController::class, 'destroy'])->middleware('auth:api');
});

