<?php

Route::group(['prefix' => 'v1'], function () {
    Route::get('prefectures', ['as' => 'prefectures', 'uses' => 'PrefectureController@index']);
    Route::get('municipalities', ['as' => 'index', 'uses' => 'MunicipalityController@index']);

    Route::group(['prefix' => 'auth', 'as' => 'auth.'], function () {
        Route::post('login', ['as' => 'login', 'uses' => 'AuthController@login']);
        Route::post('refresh', ['as' => 'refresh', 'uses' => 'AuthController@refresh']);
        Route::get('logout', ['as' => 'logout', 'uses' => 'AuthController@logout']);
        Route::post('facebook', ['as' => 'login_facebook', 'uses' => 'FacebookAuthController@login']);
    });
    Route::get('body_types', ['as' => 'bodyTypes', 'uses' => 'BodyTypeController@index']);
    Route::get('jobs', ['as' => 'jobs', 'uses' => 'JobController@index']);
});
