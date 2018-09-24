<?php

Route::group(['namespace' => 'Webview', 'prefix' => 'webview', 'as' => 'webview.'], function () {
    Route::get('card/create', ['as' => 'create', 'uses' => 'CreditCardController@create']);
    Route::group(['middleware' => ['auth', 'guest']], function () {
        Route::post('card/create', ['as' => 'add_card', 'uses' => 'CreditCardController@addCard']);
        Route::get('card/{card}', ['as' => 'show', 'uses' => 'CreditCardController@show'])->where('card', '[0-9]+');
        Route::get('card/edit/{card}', ['as' => 'edit', 'uses' => 'CreditCardController@edit'])->where('card', '[0-9]+');
    });
});

Route::group(['middleware' => 'auth'], function () {
    Route::get('/logout', 'HomeController@logout')->name('web.logout');
    Route::group(['prefix' => 'profile', 'as' => 'profile.'], function () {
        Route::get('/', ['as' => 'index', 'uses' => 'ProfileController@index']);
        Route::get('edit', ['as' => 'edit', 'uses' => 'ProfileController@edit']);
    });

    Route::group(['prefix' => 'user', 'as' => 'user.'], function () {
        Route::get('/{id}', ['as' => 'show', 'uses' => 'UserController@show'])->where('id', '[0-9]+');
    });

    Route::group(['prefix' => 'purchase', 'as' => 'purchase.'], function () {
        Route::get('/', ['as' => 'index', 'uses' => 'PointController@index']);
    });

    Route::group(['prefix' => 'rooms', 'as' => 'rooms.'], function () {
        Route::get('{room}/messages', ['as' => 'messages', 'uses' => 'MessageController@message'])->where('room', '[0-9]+');
    });

    Route::group(['prefix' => 'evaluation', 'as' => 'evaluation.'], function () {
        Route::get('/', ['as' => 'index', 'uses' => 'RatingController@index']);
    });

});

Route::get('/', 'HomeController@index')->name('web.index');
Route::get('/login/line', 'Auth\LineController@login')->name('auth.line');
Route::get('/login/line/callback', 'Auth\LineController@handleCallBack');

Route::group(['middleware' => ['auth', 'guest']], function () {
    Route::group(['prefix' => 'guest', 'as' => 'guest.'], function () {
        Route::group(['prefix' => 'orders', 'as' => 'orders.'], function () {
            Route::get('/', ['as' => 'index', 'uses' => 'OrderController@index']);
        });
    });

    Route::group(['prefix' => 'points', 'as' => 'points.'], function () {
        Route::get('/', ['as' => 'points_history', 'uses' => 'PointController@getPointsHistory']);
    });
});
