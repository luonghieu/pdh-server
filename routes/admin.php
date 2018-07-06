<?php
Route::group(['prefix' => 'admin', 'as' => 'admin.'], function () {
    Route::get('login', ['as' => 'login', 'uses' => 'AuthController@index']);
    Route::group(['namespace' => 'Account', 'prefix' => 'accounts', 'as' => 'accounts.'], function () {
        Route::get('/', ['as' => 'index', 'uses' => 'AccountController@index']);
        Route::get('/show', ['as' => 'show', 'uses' => 'AccountController@show']);
    });
});
