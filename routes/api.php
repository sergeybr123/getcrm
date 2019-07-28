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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('get-users', 'Api\ApiController@getUsers');
Route::post('change-owner', 'Api\ApiController@changeOwner');
Route::get('create-inputs-if-not-exists', 'Api\ApiController@create_inputs_if_not_exists');
