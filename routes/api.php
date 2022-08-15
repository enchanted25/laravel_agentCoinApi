<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CoinController;
use App\Models\User;
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

//public routes user
Route::get('/users', function () {
    return User::all();
});


Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);


// public routes coin
Route::get('/coins', [CoinController::class, 'index']);
Route::get('/coins/{id}', [CoinController::class, 'show']);


//protected routes
Route::group(['middleware' => ['auth:sanctum']], function () {

    //routes coin
    Route::get('coins', [CoinController::class, 'index']);
    Route::post('coins', [CoinController::class, 'store']);
    Route::delete('/coins/{id}', [CoinController::class, 'destroy']);


    //route logout
    Route::post('/logout', [AuthController::class, 'logout']);
});
