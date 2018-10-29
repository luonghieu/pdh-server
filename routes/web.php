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
        Route::get('/', ['as' => 'index', 'uses' => 'ProfileController@index'])->middleware('check_info');
        Route::get('edit', ['as' => 'edit', 'uses' => 'ProfileController@edit']);
    });

    Route::group(['middleware' => 'check_info'], function () {
        Route::group(['prefix' => 'cast', 'as' => 'cast.'], function () {
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

        Route::group(['prefix' => 'bank_account', 'as' => 'bank_account.'], function () {
            Route::get('/', ['as' => 'index', 'uses' => 'BankAccountController@index']);
            Route::get('/bank_name', ['as' => 'search_bank_name', 'uses' => 'BankAccountController@searchBankName']);
            Route::get('/bank_name/edit', ['as' => 'edit', 'uses' => 'BankAccountController@edit']);
            Route::post('/bank_name', ['as' => 'bank_name', 'uses' => 'BankAccountController@bankName']);
            Route::get('/branch_bank_name', ['as' => 'search_branch_bank_name', 'uses' => 'BankAccountController@searchBranchBankName']);
            Route::post('/branch_bank_name', ['as' => 'branch_bank_name', 'uses' => 'BankAccountController@branchBankName']);
        });

        Route::get('/cast/rank', ['as' => 'cast_rank', 'uses' => 'CastRankingController@index']);
    });
});

Route::get('/', 'HomeController@ld');
Route::get('/redirect', 'RedirectController@index');
Route::get('/login', 'HomeController@login')->name('web.login');
Route::get('/mypage', 'HomeController@index')->name('web.index');
Route::get('/login/line', 'Auth\LineController@login')->name('auth.line');
Route::get('/login/line/callback', 'Auth\LineController@handleCallBack');
Route::post('/line/webhook', 'Auth\LineController@webhook');

Route::group(['middleware' => ['auth', 'guest', 'check_info'], 'as' => 'guest.'], function () {
    Route::group(['as' => 'orders.'], function () {
        Route::get('/reserve', ['as' => 'reserve', 'uses' => 'OrderController@index']);
        Route::get('/call', ['as' => 'call', 'uses' => 'OrderController@call']);
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
        Route::get('/nominate', ['as' => 'nominate', 'uses' => 'OrderController@nominate']);
        Route::post('/nominate', ['as' => 'post_nominate', 'uses' => 'OrderController@createNominate']);
        Route::get('/nominate/step2', ['as' => 'nominate_step2', 'uses' => 'OrderController@nominateAttention']);
        Route::get('/orders/load_more', ['as' => 'orders_load_more', 'uses' => 'OrderController@loadMoreListOrder']);
        Route::get('/step3/load_more', ['as' => 'step3_load_more', 'uses' => 'OrderController@loadMoreListCast']);
    });
});

Route::group(['middleware' => ['auth', 'guest', 'check_info']], function () {
    Route::get('/history', ['as' => 'points.history', 'uses' => 'PointController@history']);
    Route::get('/point_history/more', ['as' => 'points.history.more', 'uses' => 'PointController@loadMore']);
});

Route::group(['as' => 'cast.'], function () {
    Route::get('/payments', ['as' => 'payments', 'uses' => 'PaymentController@history']);
    Route::get('/payments/load_more', ['as' => 'payments_load_more', 'uses' => 'PaymentController@loadMore']);
});
