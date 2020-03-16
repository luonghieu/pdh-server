<?php

Route::group(['namespace' => 'Webview', 'prefix' => 'webview', 'as' => 'webview.'], function () {
    Route::get('card/create', ['as' => 'create', 'uses' => 'CreditCardController@create']);
    Route::get('invite_code', ['as' => 'get_invite_code', 'uses' => 'InviteCodeController@inviteCode']);
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

        Route::group(['prefix' => 'verify', 'as' => 'verify.'], function () {
            Route::get('/', ['as' => 'index', 'uses' => 'ProfileController@verify']);
        });
    });

    Route::group(['middleware' => 'check_info'], function () {
        Route::group(['prefix' => 'purchase', 'as' => 'purchase.'], function () {
            Route::get('/', ['as' => 'index', 'uses' => 'PointController@index']);
            Route::get('/select_payment_methods', ['as' => 'select_payment_methods', 'uses' => 'PointController@selectPaymentMethods']);
        });

        Route::group(['middleware' => 'is_active', 'prefix' => 'message', 'as' => 'message.'], function () {
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
Route::post('/line_notify/webhook', ['as' => 'line_notify', 'uses' => 'Auth\LineNotifyController@webhook']);
Route::post('/telecom_credit/webhook', ['as' => 'tc_webhook', 'uses' => 'TelecomCreditController@webhook']);
Route::get('/cast_mypage', 'HomeController@castMypage')->name('web.cast_index');

Route::group(['middleware' => 'auth', 'prefix' => 'verify', 'as' => 'verify.'], function () {
    Route::get('/code', ['as' => 'code', 'uses' => 'UserController@code']);
    Route::get('/', ['as' => 'index', 'uses' => 'UserController@verify']);
});

Route::group(['middleware' => ['auth', 'guest', 'check_info'], 'as' => 'guest.'], function () {
    Route::group(['middleware' => 'is_active', 'as' => 'orders.'], function () {
        Route::get('/reserve', ['as' => 'reserve', 'uses' => 'OrderController@index']);
        Route::get('/call', ['as' => 'call', 'uses' => 'OrderController@call']);
        Route::get('/call/step2', ['as' => 'get_step2', 'uses' => 'OrderController@selectTags']);
        Route::get('/call/step3', ['as' => 'get_step3', 'uses' => 'OrderController@selectCasts']);
        Route::get('/call/step4', ['as' => 'get_step4', 'uses' => 'OrderController@attention']);
        Route::get('/cancellation_policies', ['as' => 'cancel', 'uses' => 'OrderController@cancel']);
        Route::get('/call/confirm', ['as' => 'confirm', 'uses' => 'OrderController@confirm']);
        Route::get('/nominate', ['as' => 'nominate', 'uses' => 'OrderController@nominate']);
        Route::post('/nominate', ['as' => 'post_nominate', 'uses' => 'OrderController@createNominate']);
        Route::get('/nominate/step2', ['as' => 'nominate_step2', 'uses' => 'OrderController@nominateAttention']);
        Route::get('/orders/load_more', ['as' => 'orders_load_more', 'uses' => 'OrderController@loadMoreListOrder']);
        Route::get('/step3/load_more', ['as' => 'step3_load_more', 'uses' => 'OrderController@loadMoreListCast']);
        Route::get('/cast/{id}/call', ['as' => 'cast_detail', 'uses' => 'OrderController@castDetail'])->where('id', '[0-9]+');
    });

    Route::get('/payment/transfer', ['as' => 'transfer', 'uses' => 'PaymentController@transfer']);

    Route::group(['prefix' => '/cast_offers', 'as' => 'cast_offers.'], function () {
        Route::get('/{id}', ['as' => 'index', 'uses' => 'CastOfferController@index'])->where('id', '[0-9]+');
    });
});

Route::group(['middleware' => ['auth', 'is_active'], 'as' => 'guest.'], function () {
    Route::get('/offers/{id}', ['as' => 'orders.offers', 'uses' => 'OrderController@offer'])->where('id', '[0-9]+');
    Route::get('/offers/attention', ['as' => 'orders.offers_attention', 'uses' => 'OrderController@offerAttention'])->where('id', '[0-9]+');
});

Route::group(['middleware' => ['auth', 'guest', 'check_info', 'is_active']], function () {
    Route::group(['prefix' => 'cast', 'as' => 'cast.'], function () {
        Route::get('/', ['as' => 'list_casts', 'uses' => 'UserController@listCasts']);
        Route::get('/list/more', ['as' => 'list.more', 'uses' => 'UserController@loadMoreListCasts']);
        Route::get('/favorite', ['as' => 'favorite', 'uses' => 'UserController@listCastsFavorite']);
        Route::get('/favorite/more', ['as' => 'favorite.more', 'uses' => 'UserController@loadMoreListCastsFavorite']);
        Route::get('/search', ['as' => 'search', 'uses' => 'UserController@search']);
        Route::get('/{id}', ['as' => 'show', 'uses' => 'UserController@show'])->where('id', '[0-9]+');
    });

    Route::get('/history', ['as' => 'points.history', 'uses' => 'PointController@history']);
    Route::get('/point_history/more', ['as' => 'points.history.more', 'uses' => 'PointController@loadMore']);
    // Route::group(['prefix' => 'invite_code', 'as' => 'invite_code.'], function () {
    //     Route::get('/', ['as' => 'get_invite_code', 'uses' => 'InviteCodeController@inviteCode']);
    // });
});

Route::group(['middleware' => ['auth', 'cast'], 'prefix' => 'cast_mypage'], function () {
    Route::group(['prefix' => 'transfer_history', 'as' => 'cast.'], function () {
        Route::get('/', ['as' => 'transfer_history', 'uses' => 'PaymentController@history']);
        Route::get('/load_more', ['as' => 'transfer_history_load_more', 'uses' => 'PaymentController@loadMore']);
    });

    Route::group(['as' => 'cast_mypage.'], function () {
        Route::group(['prefix' => 'bank_account', 'as' => 'bank_account.'], function () {
            Route::get('/', ['as' => 'index', 'uses' => 'BankAccountController@index']);
            Route::get('/edit/bank', ['as' => 'search_bank_name', 'uses' => 'BankAccountController@searchBankName']);
            Route::get('/edit', ['as' => 'edit', 'uses' => 'BankAccountController@edit']);
            Route::post('/edit/bank', ['as' => 'bank_name', 'uses' => 'BankAccountController@bankName']);
            Route::get('/edit/branch', ['as' => 'search_branch_bank_name', 'uses' => 'BankAccountController@searchBranchBankName']);
            Route::post('/edit/branch', ['as' => 'branch_bank_name', 'uses' => 'BankAccountController@branchBankName']);
        });
    });
});

Route::group(['middleware' => ['auth', 'guest', 'check_info'], 'prefix' => 'timelines', 'as' => 'web.'], function () {
    Route::group(['as' => 'timelines.'], function () {
        Route::get('/', ['as' => 'index', 'uses' => 'TimeLineController@index']);
        Route::get('/create', ['as' => 'create', 'uses' => 'TimeLineController@create']);
        Route::get('/load_more', ['as' => 'load_more', 'uses' => 'TimeLineController@loadMoreListTimelines']);
        Route::get('/{id}', ['as' => 'show', 'uses' => 'TimeLineController@show']);
        Route::get('/favorites/load_more', ['as' => 'favorites.load_more', 'uses' => 'TimeLineController@loadMoreFavorites']);
    });
});

Route::group(['middleware' => ['auth'], 'prefix' => 'guest', 'as' => 'guest.'], function () {
    Route::get('/{id}', ['as' => 'show', 'uses' => 'UserController@show'])->where('id', '[0-9]+');
});

Route::group(['middleware' => ['auth'], 'prefix' => 'resigns', 'as' => 'resigns.'], function () {
    Route::get('/reason', ['as' => 'reason', 'uses' => 'ResignController@reason']);
    Route::get('/confirm', ['as' => 'confirm', 'uses' => 'ResignController@confirm']);
    Route::get('/complete', ['as' => 'complete', 'uses' => 'ResignController@complete']);
});

Route::view('tc_register_card', 'web.cards.telecom_credit');
