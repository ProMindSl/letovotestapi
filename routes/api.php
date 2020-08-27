<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Auth API Routes
|--------------------------------------------------------------------------
*/

Route::group([

    'middleware' => 'api',
    'prefix' => 'auth'

], function ($router) {

    Route::post('login', 'AuthController@login');
    Route::post('logout', 'AuthController@logout');
    Route::post('refresh', 'AuthController@refresh');
    Route::post('me', 'AuthController@me');

});

/*
|--------------------------------------------------------------------------
| CRUD API Routes
|--------------------------------------------------------------------------
*/
Route::group([
    'middleware' => 'api'
], function () {

    Route::apiResources(
        [
            'students' => 'StudentController'
        ]);
});
