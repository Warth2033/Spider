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

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });


// Route::group([

//     'prefix' => 'auth'

// ], function ($router) {

//     Route::apiResource('/users', 'UsersController');
//     Route::post('/users', 'UsersController@index');

//     Route::apiResource('/losts', 'LostController');
//     Route::patch('/losts/{lost_id}', function ($lost_id) {
//     App::call('App\Http\Controllers\LostController@update', [$lost_id]);
//     });

//     Route::apiResource('/founds', 'FoundController');
//     Route::patch('/founds/{found_id}', function ($found_id) {
//     App::call('App\Http\Controllers\FoundController@update', [$found_id]);
// });

// });

Route::apiResource('/users', 'UsersController');
    Route::post('/users', 'UsersController@index');

    Route::apiResource('/losts', 'LostController');
    Route::patch('/losts/{lost_id}', function ($lost_id) {
    App::call('App\Http\Controllers\LostController@update', [$lost_id]);
    });

    Route::apiResource('/founds', 'FoundController');
    Route::patch('/founds/{found_id}', function ($found_id) {
    App::call('App\Http\Controllers\FoundController@update', [$found_id]);
});