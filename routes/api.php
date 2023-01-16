<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\customer_loan_api\CustomerLoanController;

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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });


Route::get('get_customer_loan_api_token', 'App\Http\Controllers\customer_loan_api\CustomerLoanController@get_api_token');

Route::post('create_customer_loan', 'App\Http\Controllers\customer_loan_api\CustomerLoanController@create_customer_loan');
Route::post('get_loan_list', 'App\Http\Controllers\customer_loan_api\CustomerLoanController@get_loan_list');

Route::post('create_customer_loan_request', 'App\Http\Controllers\customer_loan_api\CustomerLoanController@create_customer_loan_request');
Route::post('get_customer_loan_requests', 'App\Http\Controllers\customer_loan_api\CustomerLoanController@get_customer_loan_requests');

Route::post('customer_loan_schedule_payment', 'App\Http\Controllers\customer_loan_api\CustomerLoanController@customer_loan_schedule_payment_add');

Route::post('customer_loan_request_approve', 'App\Http\Controllers\customer_loan_api\CustomerLoanController@customer_approve_loan');