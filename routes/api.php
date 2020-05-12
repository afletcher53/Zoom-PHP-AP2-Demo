<?php

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('ZoomAPI2/users', 'ZoomAPIV2@users');
Route::get('ZoomAPI2/users/{userId}', 'ZoomAPIV2@listwebinars');
Route::get('ZoomAPI2/webinars/{webinarId}', 'ZoomAPIV2@getwebinars');
Route::get('ZoomAPI2/metrics/webinars/{webinarId}/participants', 'ZoomAPIV2@getwebinarparticipants');
Route::get('ZoomAPI2/metrics/webinars', 'ZoomAPIV2@listallwebinars');
