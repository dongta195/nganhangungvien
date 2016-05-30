<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

/*Route::match(['get', 'post'], '/', [
	'as' => 'index', 'uses' => 'WelcomeController@index'
]);*/

Route::match(['get', 'post'], '/admin', [
	'as' => 'index', 'uses' => 'WelcomeController@indexAdmin'
]);

/**
 * Authentication
 */
Route::controllers([
		'auth' => 'Auth\AuthController',
		'password' => 'Auth\PasswordController',
]);

Route::group(['middleware' => ['auth', 'roles'], 'roles' => ['manager']], function() {
	
	Route::match(['get', 'post'], '/user/list', [
		'as' => 'user.list', 'uses' => 'UserController@userList'
	]);
	
	Route::match(['get', 'post'], '/user/form', [
		'as' => 'user.form', 'uses' => 'UserController@userForm'
	]);
	
	Route::post('/user/delete/{id}', [
		'as' => 'user.delete', 'uses' => 'UserController@delete'
	]);
});

/**
 * Frontend
 */
Route::group(['prefix' => ''], function()
{
	Route::get('/', 'Front\HomeController@index');

	Route::match(['get', 'post'], '/tim-kiem-ung-vien', [
		'as' => 'candidate.search', 'uses' => 'Front\SearchController@index'
	]);
	
	Route::match(['get', 'post'], '/ho-so/{slug}_{id}', [
		'as' => 'candidate.profile', 'uses' => 'Front\CandidateProfileController@index'
	]);
});

Route::match(['get', 'post'], '/candidate/form', [
	'as' => 'candidate.form', 'uses' => 'Front\CandidateController@candidateForm'
]);

Route::match(['get', 'post'], '/new/form', [
	'as' => 'new.form', 'uses' => 'Front\NewController@newForm'
]);
