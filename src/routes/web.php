<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::post('/order/insert-coin', [\App\Http\Controllers\OrderController::class, 'insertCoin']);
Route::post('/order/choose-product', [\App\Http\Controllers\OrderController::class, 'chooseProduct']);
Route::get('/order/return-coin', [\App\Http\Controllers\OrderController::class, 'returnCoin']);

Route::get('/service/info', [\App\Http\Controllers\ServiceController::class, 'info']);
Route::post('/service/add-product', [\App\Http\Controllers\ServiceController::class, 'addProduct']);
Route::post('/service/add-coin', [\App\Http\Controllers\ServiceController::class, 'addCoin']);
Route::get('/service/collect-coins', [\App\Http\Controllers\ServiceController::class, 'collectCoins']);