<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
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

Route::group(['middleware' => 'auth:api'], function () {
    Route::post('/auth/revoke', [AuthController::class, 'revokeToken']);
    Route::get('/auth/whoami', [AuthController::class, 'whoami']);

    Route::get('/menu', [ProductController::class, 'menuIndex']);
    Route::get('/orders', [OrderController::class, 'indexOrders']);
    Route::post('/orders', [OrderController::class, 'placeOrder']);
    Route::delete('/orders/{order}', [OrderController::class, 'cancelOrder']);
    Route::patch('/orders/{order}', [OrderController::class, 'updateOrder']);
});

Route::post('/auth/login', [AuthController::class, 'login']);
Route::post('/auth/register', [AuthController::class, 'register']);
