<?php
Route::group(['prefix' => 'admin', 'as' => 'admin.'], function () {
    Route::get('login', ['as' => 'login', 'uses' => 'AuthController@index']);
    Route::post('login', ['as' => 'login', 'uses' => 'AuthController@login']);
    Route::get('logout', ['as' => 'logout', 'uses' => 'AuthController@logout']);
    Route::group(['namespace' => 'User', 'prefix' => 'users', 'as' => 'users.', 'middleware' => 'is_admin'], function () {
        Route::get('/', ['as' => 'index', 'uses' => 'UserController@index']);
        Route::get('{user}', ['as' => 'show', 'uses' => 'UserController@show'])->where('user', '[0-9]+');
        Route::put('{user}', ['as' => 'change_active', 'uses' => 'UserController@changeActive'])->where('user', '[0-9]+');
        Route::post('{user}', ['as' => 'change_cast_class', 'uses' => 'UserController@changeCastClass'])->where('user', '[0-9]+');
    });

    Route::group(['namespace' => 'Cast', 'prefix' => 'casts', 'as' => 'casts.', 'middleware' => 'is_admin'], function () {
        Route::get('/', ['as' => 'index', 'uses' => 'CastController@index']);
        Route::get('{user}/register', ['as' => 'register', 'uses' => 'CastController@registerCast']);
        Route::post('{user}/confirm', ['as' => 'confirm', 'uses' => 'CastController@confirmRegister']);
        Route::post('{user}/save', ['as' => 'save', 'uses' => 'CastController@saveCast']);
    });

    Route::group(['namespace' => 'Cast_Ranking', 'prefix' => 'cast_rankings', 'as' => 'cast_rankings.', 'middleware' => 'is_admin'], function () {
        Route::get('/', ['as' => 'index', 'uses' => 'CastRankingController@index']);
    });
});
