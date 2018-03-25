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

Route::get('/payments/operations', 'PaymentController@operations');
Route::get('/payments/download', 'PaymentController@download');
Route::post('/payments/recharge', 'PaymentController@recharge');

Route::middleware(\App\Http\Middleware\SimpleAuth::class)->post('/payments/transfer', 'PaymentController@transfer');


