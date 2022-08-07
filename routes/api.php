<?php

use App\Http\Controllers\AgentController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CoinController;
use App\Models\User;
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

// Route::get('/coins', function () {
//     return Coin::all();
// });

// Route::resource('coins', CoinController::class);


//public routes user
Route::get('/users', function () {
    return User::all();
});


Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
// Route::put('/update/{id}', [AuthController::class, 'update']);



// public routes coin
Route::get('/coins', [CoinController::class, 'index']);
Route::get('/coins/{id}', [CoinController::class, 'show']);






//protected routes user
Route::group(['middleware' => ['auth:sanctum']], function () {

    //routes coin
    Route::get('coins', [CoinController::class, 'index']);
    Route::post('coins', [CoinController::class, 'store']);
    // Route::put('/coins/{id}', [CoinController::class, 'update']);
    Route::delete('/coins/{id}', [CoinController::class, 'destroy']);


    //route logout
    Route::post('/logout', [AuthController::class, 'logout']);
});
