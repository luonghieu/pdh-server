<?php

Route::group(['prefix' => 'v1'], function () {
    Route::group(['prefix' => 'auth', 'as' => 'auth.'], function () {
        Route::post('login', ['as' => 'login', 'uses' => 'AuthController@login']);
        Route::post('refresh', ['as' => 'refresh', 'uses' => 'AuthController@refresh']);
        Route::get('logout', ['as' => 'logout', 'uses' => 'AuthController@logout']);
    });
});
