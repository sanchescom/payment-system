<?php

use Illuminate\Http\Request;

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

//Route::middleware('auth:api')->get('/user', function (Request $request) {
//    return $request->user();
//});


Route::post('/users', 'UserController@create');

Route::patch('/currencies', 'CurrencyController@upload');

Route::middleware(\App\Http\Middleware\SimpleAuth::class)->post('/payment/transfer', 'PaymentController@transfer');
Route::middleware(\App\Http\Middleware\SimpleAuth::class)->post('/payment/recharge', 'PaymentController@recharge');


