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
    Route::get('/logout', 'HomeController@logout');
    Route::group(['prefix' => 'profile', 'as' => 'profile.'], function () {
        Route::get('/', ['as' => 'index', 'uses' => 'ProfileController@index']);
        Route::get('edit', ['as' => 'edit', 'uses' => 'ProfileController@edit']);
    });

    Route::group(['prefix' => 'user', 'as' => 'user.'], function () {
        Route::get('/{id}', ['as' => 'show', 'uses' => 'UserController@show'])->where('id', '[0-9]+');
    });

    Route::group(['prefix' => 'points', 'as' => 'points.'], function () {
        Route::get('/', ['as' => 'index', 'uses' => 'PointController@index']);
    });

    Route::group(['prefix' => 'rooms', 'as' => 'rooms.'], function () {
        Route::get('{room}/messages', ['as' => 'messages', 'uses' => 'MessageController@message'])->where('room', '[0-9]+');
    });
});

Route::get('/', 'HomeController@index')->name('web.index');
Route::get('/login/line', 'Auth\LineController@login')->name('auth.line');
Route::get('/login/line/callback', 'Auth\LineController@handleCallBack');

Route::group(['middleware' => ['auth', 'guest'], 'as' => 'guest.'], function () {
    Route::group(['as' => 'orders.'], function () {
        Route::get('/', ['as' => 'index', 'uses' => 'OrderController@index']);
        Route::get('/call', ['as' => 'call', 'uses' => 'OrderController@call']);
        Route::post('/get_day', ['as' => 'get_day', 'uses' => 'OrderController@getDayOfMonth']);
        Route::post('/call', ['as' => 'post_call', 'uses' => 'OrderController@getParams']);
        Route::get('/call/step2', ['as' => 'get_step2', 'uses' => 'OrderController@selectTags']);
        Route::post('/call/step2', ['as' => 'post_step2', 'uses' => 'OrderController@getTags']);
        Route::get('/call/step3', ['as' => 'get_step3', 'uses' => 'OrderController@selectCasts']);
        Route::post('/call/step3', ['as' => 'post_step3', 'uses' => 'OrderController@getSelectCasts']);
        Route::get('/call/step4', ['as' => 'get_step4', 'uses' => 'OrderController@attention']);
        Route::get('/cancel', ['as' => 'cancel', 'uses' => 'OrderController@cancel']);
        Route::get('/call/confirm', ['as' => 'get_confirm', 'uses' => 'OrderController@confirm']);
        Route::post('/call/confirm', ['as' => 'post_confirm', 'uses' => 'OrderController@getConfirm']);
        Route::post('/call/add', ['as' => 'add', 'uses' => 'OrderController@add']);
    });
});
