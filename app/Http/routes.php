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

Route::get('/', function () {
    return view('welcome');
});

Route::group(array('prefix' => 'students'), function() {
    Route::post('register','StudentController@register');
    Route::post('login','StudentController@login');
    Route::get('view','StudentController@view');
    Route::get('profile/{id}','StudentController@profile');
    Route::put('edit/{id}','StudentController@edit');
    Route::delete('delete/{id}','StudentController@delete');
    Route::get('search','StudentController@search');

});



