<?php

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

Route::prefix('v1')->group(function () {
    Route::get('gpx-listing', 'Api\v1\GpxController@listing');
    Route::post('gpx-add', 'Api\v1\GpxController@add');
    Route::get('gpx-remove/{id}', 'Api\v1\GpxController@remove');
});