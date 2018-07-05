<?php
Route::group(['prefix' => 'admin', 'as' => 'admin.'], function () {
    Route::get('login', ['as' => 'login', 'uses' => 'AuthController@index']);
    Route::post('login', ['as' => 'login', 'uses' => 'AuthController@login']);
    Route::get('logout', ['as' => 'logout', 'uses' => 'AuthController@logout']);
    Route::group(['namespace' => 'Account', 'prefix' => 'accounts', 'as' => 'accounts.', 'middleware' => 'is_admin'], function () {
        Route::get('/', ['as' => 'index', 'uses' => 'AccountController@index']);
        Route::get('/show', ['as' => 'show', 'uses' => 'AccountController@show']);
    });
});
