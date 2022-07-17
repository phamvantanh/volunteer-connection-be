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
  // 'prefix' => 'auth'

], function () {
  Route::post('/login', 'AuthController@login');
  Route::post('/register', 'AuthController@register');
  Route::post('/logout', 'AuthController@logout');
  Route::post('/refresh', 'AuthController@refresh');
  Route::get('/email/verify', 'VerificationController@show')->name('verification.notice');
  Route::get('/email/verify/{id}', 'VerificationController@verify')->name('verification.verify');
  Route::post('/email/resend', 'VerificationController@resend')->name('verification.resend');
  Route::get('/posts/search/{param}', 'PostController@searchByTitleOrContent');
  Route::get('/posts/{param}', 'PostController@show');
  Route::get('/events/search/{param}', 'EventController@searchByTitleOrContent');
  Route::get('/events/{param}', 'EventController@show');

  Route::get('/follow/volunteer/{id}', 'FollowingController@getFollowingOfVolunteer');
  Route::get('/follow/organization/{id}', 'FollowingController@getFollowingOfOrganization');

  Route::get('/user/{param}', 'UserController@show');

  Route::get('/homepage/posts', 'HomePageController@getPostList');
  Route::get('/homepage/events', 'HomePageController@getEventList');

  Route::get('/user/{param}/event', 'UserController@myEvent');
  Route::get('/user/{param}/post', 'UserController@myPost');
  Route::get('/user/{param}/register-event', 'UserController@myRegisteredEvent');

  Route::get('/certificate/{param}', 'CertificateController@show');
  // Route::get('/certificates', 'CertificateController@index');
  Route::get('/user/{param}/certificate', 'UserController@myCertificate');
  
  // Route::get('/event/{id}/comments', 'EventController@fetchComments');
  Route::get('/event/{id}/comments', 'EventController@fetchComments2');

  Route::get('/event/{id}/reviews', 'EventController@fetchReviews');

});

Route::group([
  'middleware' => ['auth:api']

], function () {
  Route::get('/user-profile', 'AuthController@userProfile');
  Route::post('/change-pass', 'AuthController@changePassWord');
  Route::put('/update', 'AuthController@update');

  Route::get('/posts', 'PostController@index');
  Route::post('/post',  'PostController@store');
  Route::put('/post/{id}',  'PostController@update');
  Route::delete('/post/{id}',  'PostController@destroy');
  
  Route::resources(['categories' => 'CategoryController']);

  Route::get('/events', 'EventController@index');
  Route::post('/event',  'EventController@store');
  Route::put('/event/{id}',  'EventController@update');
  Route::delete('/event/{id}',  'EventController@destroy');

  Route::get('/category/{id}/events', 'CategoryController@getEventofCategory');

  Route::get('/register-list/{id}', 'EventController@getRegisterListOfEvent');
  Route::post('/register-list/delete', 'RegisteredVolunteerController@removeVolunteer');
  Route::put('/register-list/update', 'RegisteredVolunteerController@update');

  Route::resources(['reports' => 'PostReportController']);

  Route::get('/bookmarked/posts', 'BookmarkedPostController@getBookmarkedPostsOfUsers');
  Route::post('/bookmarked/post',  'BookmarkedPostController@store');
  Route::delete('/bookmarked/post/{post_id}',  'BookmarkedPostController@destroy');

  Route::post('/register/event',  'RegisteredVolunteerController@store');
  Route::delete('/register/event/{event_id}',  'RegisteredVolunteerController@destroy');
  
  Route::get('/bookmarked/events', 'BookmarkedEventController@getBookmarkedEventsOfUsers');
  Route::post('/bookmarked/event',  'BookmarkedEventController@store');
  Route::delete('/bookmarked/event/{event_id}',  'BookmarkedEventController@destroy');

  Route::post('/follow', 'FollowingController@store');
  Route::delete('/follow/{id}',  'FollowingController@destroy');

  Route::get('/admin', 'AdminController@getDashBoardInfo');

  Route::get('/users', 'UserController@index');
  Route::post('/user',  'UserController@store');
  Route::delete('/user/{id}',  'UserController@destroy');
  Route::put('/user/{id}',  'UserController@update');

  Route::delete('/comments/{id}',  'CommentController@destroy');
  Route::put('/comments/{id}',  'CommentController@update');
  Route::post('/comments', 'CommentController@store');

  Route::delete('/reviews/{id}',  'ReviewController@destroy');
  Route::put('/reviews/{id}',  'ReviewController@update');
  Route::post('/reviews', 'ReviewController@store');

  Route::post('/upload', 'AdminController@saveImage');



});

