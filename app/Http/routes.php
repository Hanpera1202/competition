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

Route::post('users', 'UserController@postCreate');
Route::post('users/{user_unique_id}', 'UserController@postUpdate');
Route::get('users/{user_unique_id}/results', 'UserController@getResults');
Route::get('users/{user_unique_id}/results/{competition_id}', 'UserController@getResult');
Route::post('users/{user_unique_id}/application', 'UserController@postApplication');

Route::get('competitions', 'CompetitionController@getIndex');

Route::get('contents/terms', 'ContentController@getTerms');

