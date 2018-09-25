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

    Route::group(['prefix' => 'message', 'as' => 'message.'], function () {
        Route::get('/', ['as' => 'index', 'uses' => 'RoomController@index']);

        Route::get('{room}', ['as' => 'messages', 'uses' => 'MessageController@message'])->where('room', '[0-9]+');
    });

    Route::group(['prefix' => 'evaluation', 'as' => 'evaluation.'], function () {
        Route::get('/', ['as' => 'index', 'uses' => 'RatingController@index']);
    });

    Route::group(['prefix' => 'history', 'as' => 'history.'], function () {
        Route::get('/{orderId}', ['as' => 'show', 'uses' => 'OrderController@history']);
    });

    Route::group(['middleware' => ['guest'], 'prefix' => 'credit_card', 'as' => 'credit_card.'], function () {
        Route::get('/', ['as' => 'index', 'uses' => 'CardController@index']);
        Route::get('edit', ['as' => 'update', 'uses' => 'CardController@update']);
    });

    Route::group(['prefix' => 'point_settement', 'as' => 'point_settement.'], function () {
        Route::post('/{orderId}', ['as' => 'create', 'uses' => 'OrderController@pointSettlement']);
    });
});

Route::get('/', 'HomeController@index')->name('web.index');
Route::get('/login/line', 'Auth\LineController@login')->name('auth.line');
Route::get('/login/line/callback', 'Auth\LineController@handleCallBack');

Route::group(['middleware' => ['auth', 'guest'], 'as' => 'guest.'], function () {
    Route::group(['as' => 'orders.'], function () {
        Route::get('/reserve', ['as' => 'reserve', 'uses' => 'OrderController@index']);
        Route::get('/call', ['as' => 'call', 'uses' => 'OrderController@call']);
        Route::post('/get_day', ['as' => 'get_day', 'uses' => 'OrderController@getDayOfMonth']);
        Route::post('/call', ['as' => 'post_call', 'uses' => 'OrderController@getParams']);
        Route::get('/call/step2', ['as' => 'get_step2', 'uses' => 'OrderController@selectTags']);
        Route::post('/call/step2', ['as' => 'post_step2', 'uses' => 'OrderController@getTags']);
        Route::get('/call/step3', ['as' => 'get_step3', 'uses' => 'OrderController@selectCasts']);
        Route::post('/call/step3', ['as' => 'post_step3', 'uses' => 'OrderController@getSelectCasts']);
        Route::get('/call/step4', ['as' => 'get_step4', 'uses' => 'OrderController@attention']);
        Route::get('/cancellation_policies', ['as' => 'cancel', 'uses' => 'OrderController@cancel']);
        Route::get('/call/confirm', ['as' => 'get_confirm', 'uses' => 'OrderController@confirm']);
        Route::post('/call/confirm', ['as' => 'post_confirm', 'uses' => 'OrderController@getConfirm']);
        Route::post('/call/add', ['as' => 'add', 'uses' => 'OrderController@add']);
    });
});

Route::group(['middleware' => ['auth', 'guest']], function () {
    Route::get('/history', ['as' => 'points.history', 'uses' => 'PointController@history']);
});
