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

	Route::post('register', 'Auth\RegisterController@register');
	Route::post('login', 'Auth\LoginController@login');


	Route::middleware('auth:api')->group(function () {
		
	    Route::post('vacancy-book', 'VacancyController@book');
	    Route::post('vacancy-unbook', 'VacancyController@unbook');
		Route::get('vacancy/{vacancy_id}/{organization_id}', 'VacancyController@setOrganization');
	    Route::get('stats/vacancy', 'VacancyController@indexStats'); 

		Route::apiResource('/vacancy', 'VacancyController');


		Route::get('organization/{id}/{creator_id}', 'OrganizationController@setCreator');
	    Route::get('stats/organization', 'OrganizationController@indexStats');

		Route::apiResource('/organization', 'OrganizationController');

   		Route::post('user/update/pass', 'UserController@updatePassword');		
	    Route::get('stats/user', 'UserController@indexStats');
	     
		Route::apiResource('user', 'UserController');

		Route::post('logout', 'Auth\LoginController@logout');

	});


