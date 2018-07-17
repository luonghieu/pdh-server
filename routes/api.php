<?php

Route::group(['prefix' => 'v1'], function () {
    Route::get('prefectures', ['as' => 'prefectures', 'uses' => 'PrefectureController@index']);
    Route::get('hometowns', ['as' => 'hometowns', 'uses' => 'PrefectureController@getHometowns']);
    Route::get('municipalities', ['as' => 'municipalities', 'uses' => 'MunicipalityController@index']);
    Route::get('salaries', ['as' => 'salaries', 'uses' => 'SalaryController@index']);
    Route::get('cast_classes', ['as' => 'cast_classes', 'uses' => 'CastClassController@index']);
    Route::get('jobs', ['as' => 'jobs', 'uses' => 'JobController@index']);
    Route::get('body_types', ['as' => 'body_types', 'uses' => 'BodyTypeController@index']);

    Route::group(['prefix' => 'auth', 'as' => 'auth.'], function () {
        Route::post('login', ['as' => 'login', 'uses' => 'AuthController@login']);
        Route::post('refresh', ['as' => 'refresh', 'uses' => 'AuthController@refresh']);
        Route::get('logout', ['as' => 'logout', 'uses' => 'AuthController@logout']);
        Route::post('facebook', ['as' => 'login_facebook', 'uses' => 'FacebookAuthController@login']);

        Route::group(['middleware' => ['auth:api']], function () {
            Route::get('me', ['as' => 'me', 'uses' => 'AuthController@me']);
            Route::post('update', ['as' => 'update', 'uses' => 'AuthController@update']);
        });
    });

    Route::group(['middleware' => ['auth:api'], 'prefix' => 'casts', 'as' => 'casts.'], function () {
        Route::get('/', ['as' => 'index', 'uses' => 'CastController@index']);
    });

    Route::group(['middleware' => ['auth:api'], 'prefix' => 'guests', 'as' => 'guests.'], function () {
        Route::get('/', ['as' => 'index', 'uses' => 'GuestController@index']);
    });

    Route::group(['middleware' => ['auth:api']], function () {
        Route::post('favorites/{id}', ['as' => 'favorite', 'uses' => 'FavoriteController@favorite']);
        Route::post('blocks/{id}', ['as' => 'block', 'uses' => 'BlockController@block']);
        Route::post('reports/{id}', ['as' => 'report', 'uses' => 'ReportController@report']);
    });

    Route::group(['middleware' => ['auth:api'], 'prefix' => 'users', 'as' => 'users.'], function () {
        Route::get('/{id}', ['as' => 'show', 'uses' => 'UserController@show']);
    });

    Route::group(['middleware' => ['auth:api'], 'prefix' => 'avatars', 'as' => 'avatars.'], function () {
        Route::post('/', ['as' => 'upload', 'uses' => 'AvatarController@upload']);
        Route::patch('/{id}', ['as' => 'set_avatar_default', 'uses' => 'AvatarController@setAvatarDefault']);
        Route::delete('/{id}', ['as' => 'delete', 'uses' => 'AvatarController@delete']);
    });

    Route::group(['middleware' => ['auth:api'], 'prefix' => 'cast_rankings', 'as' => 'cast_rankings.'], function () {
        Route::get('/', ['as' => 'show', 'uses' => 'CastRankingController@index']);
    });

});
