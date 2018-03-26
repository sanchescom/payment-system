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


Route::get('/users', 'UserController@getAll');
Route::post('/users', 'UserController@createNew');

Route::patch('/currencies', 'CurrencyController@uploadRates');

Route::get('/payments/operations', 'PaymentController@getAllOperations');
Route::get('/payments/download', 'PaymentController@downloadAllOperations');
Route::post('/payments/recharge', 'PaymentController@rechargeAccount');

Route::middleware(\App\Http\Middleware\SimpleAuth::class)->post('/payments/transfer', 'PaymentController@transferMoney');


