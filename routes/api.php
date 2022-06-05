<?php

use Illuminate\Http\Request;
// use App\Http\Controllers\UserConttroller;
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


header('Access-Control-Allow-Methods: GET, POST, PATCH, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Origin, Content-Type, X-Auth-Token, Authorization, Accept,charset,boundary,Content-Length');
header('Access-Control-Allow-Origin: *');

Route::group([
  'middleware' => ['api'],
  'prefix' => 'auth'

], function () {
  Route::post('/login', 'AuthController@login');
  Route::post('/register', 'AuthController@register');
  Route::post('/logout', 'AuthController@logout');
  Route::post('/refresh', 'AuthController@refresh');
  Route::get('/email/verify', 'VerificationController@show')->name('verification.notice');
  Route::get('/email/verify/{id}', 'VerificationController@verify')->name('verification.verify');
  Route::post('/email/resend', 'VerificationController@resend')->name('verification.resend');

  // Route::resources(['campaigns'=>'CampaignController']);

  Route::group([
    'middleware' => ['auth:api']

  ], function () {
    Route::get('/user-profile', 'AuthController@userProfile');
    Route::post('/change-pass', 'AuthController@changePassWord');
    Route::put('/update', 'UserController@update');
  });
});
