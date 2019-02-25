<?php

Route::group(['prefix' => 'v1'], function () {
    Route::get('prefectures', ['as' => 'prefectures', 'uses' => 'PrefectureController@index']);
    Route::get('hometowns', ['as' => 'hometowns', 'uses' => 'PrefectureController@getHometowns']);
    Route::get('municipalities', ['as' => 'municipalities', 'uses' => 'MunicipalityController@index']);
    Route::get('salaries', ['as' => 'salaries', 'uses' => 'SalaryController@index']);
    Route::get('cast_classes', ['as' => 'cast_classes', 'uses' => 'CastClassController@index']);
    Route::get('jobs', ['as' => 'jobs', 'uses' => 'JobController@index']);
    Route::get('body_types', ['as' => 'body_types', 'uses' => 'BodyTypeController@index']);
    Route::get('tags', ['as' => 'tags', 'uses' => 'TagController@index']);
    Route::get('glossaries', ['as' => 'glossaries', 'uses' => 'GlossaryController@glossary']);
    Route::post('/get_day', ['as' => 'get_day', 'uses' => 'OrderController@getDayOfMonth']);
    Route::get('post_code', ['as' => 'post_code', 'uses' => 'PostCodeController@find']);
    Route::get('voices/code', ['as' => 'voice_code', 'uses' => 'VoiceController@code']);
    Route::get('voices/code_repeat', ['as' => 'voice_code_repeat', 'uses' => 'VoiceController@repeat']);
    Route::get('versions', ['as' => 'versions', 'uses' => 'VersionController@index']);

    Route::group(['prefix' => 'auth', 'as' => 'auth.'], function () {
        Route::post('login', ['as' => 'login', 'uses' => 'AuthController@login']);
        Route::post('refresh', ['as' => 'refresh', 'uses' => 'AuthController@refresh']);
        Route::get('logout', ['as' => 'logout', 'uses' => 'AuthController@logout']);
        Route::post('facebook', ['as' => 'login_facebook', 'uses' => 'FacebookAuthController@login']);
        Route::post('line', ['as' => 'login_line', 'uses' => 'LineAuthController@login']);

        Route::group(['middleware' => ['auth:api']], function () {
            Route::get('me', ['as' => 'me', 'uses' => 'AuthController@me']);
            Route::post('update', ['as' => 'update', 'uses' => 'AuthController@update']);

            Route::post('/verify_code', ['as' => 'code', 'uses' => 'VerificationController@code']);
            Route::post('/resend_code', ['as' => 'resend', 'uses' => 'VerificationController@resend']);
            Route::post('/verify', ['as' => 'verify', 'uses' => 'VerificationController@verify']);
            Route::post('/send_code_by_call', ['as' => 'send_code_by_call', 'uses' => 'VerificationController@sendCodeByCall']);
        });
    });

    Route::group(['middleware' => ['auth:api'], 'prefix' => 'casts', 'as' => 'casts.'], function () {
        Route::get('/', ['as' => 'index', 'uses' => 'CastController@index']);
        Route::post('/list_casts', ['as' => 'list_casts', 'uses' => 'CastController@listCasts']);
    });

    Route::group(['middleware' => ['auth:api'], 'prefix' => 'guests', 'as' => 'guests.'], function () {
        Route::get('/', ['as' => 'index', 'uses' => 'GuestController@index']);
    });

    Route::group(['middleware' => ['auth:api']], function () {
        Route::post('favorites/{id}', ['as' => 'favorite', 'uses' => 'FavoriteController@favorite']);
        Route::post('blocks/{id}', ['as' => 'block', 'uses' => 'BlockController@block']);
        Route::post('reports/{id}', ['as' => 'report', 'uses' => 'ReportController@report']);
        Route::get('cast_rankings', ['as' => 'cast_rankings', 'uses' => 'CastRankingController@index']);
        Route::delete('messages/{id}', ['as' => 'messages', 'uses' => 'MessageController@delete']);
        Route::patch('working_today', ['as' => 'working_today', 'uses' => 'WorkingTodayController@update']);
        Route::post('/ratings', ['as' => 'create_rating', 'uses' => 'RatingController@create']);
        Route::get('/notifications/{id}', ['as' => 'show_notifications', 'uses' => 'NotificationController@show']);
        Route::get('/notifications', ['as' => 'notifications', 'uses' => 'NotificationController@index']);
    });

    Route::group(['middleware' => ['auth:api'], 'prefix' => 'users', 'as' => 'users.'], function () {
        Route::get('/{id}', ['as' => 'show', 'uses' => 'UserController@show']);
    });

    Route::group(['middleware' => ['auth:api'], 'prefix' => 'avatars', 'as' => 'avatars.'], function () {
        Route::post('/', ['as' => 'upload', 'uses' => 'AvatarController@upload']);
        Route::post('/{id}', ['as' => 'update', 'uses' => 'AvatarController@update']);
        Route::patch('/{id}', ['as' => 'set_avatar_default', 'uses' => 'AvatarController@setAvatarDefault']);
        Route::delete('/{id}', ['as' => 'delete', 'uses' => 'AvatarController@delete']);
    });

    Route::group(['middleware' => ['auth:api'], 'prefix' => 'rooms', 'as' => 'rooms.'], function () {
        Route::get('/', ['as' => 'room', 'uses' => 'RoomController@index']);
        Route::get('/list_room', ['as' => 'list_room', 'uses' => 'RoomController@getListRoom']);
        Route::post('/', ['as' => 'create', 'uses' => 'RoomController@store']);
        Route::get('/{id}', ['as' => 'index', 'uses' => 'MessageController@index']);
        Route::post('{id}/messages', ['as' => 'store', 'uses' => 'MessageController@store']);
        Route::get('admin/room_load', ['as' => 'roomLoad', 'uses' => 'RoomController@getRooms']);
        Route::get('admin/room_detail/{id}', ['as' => 'room_detail', 'uses' => 'RoomController@getRoom']);
        Route::get('admin/unread_messages', ['as' => 'admin_unread_messages', 'uses' => 'RoomController@getAdminUnreadMessages']);
    });

    Route::group(['middleware' => ['auth:api', 'guest'], 'prefix' => 'guest', 'as' => 'guest.'], function () {
        Route::get('/cast_histories', ['as' => 'cast_histories', 'uses' => 'Guest\GuestController@castHistories']);
        Route::post('/request_transfer', ['as' => 'request_transfer', 'uses' => 'Guest\GuestController@requestTransfer']);

        Route::group(['prefix' => 'orders'], function () {
            Route::get('/', ['as' => 'index', 'uses' => 'Guest\OrderController@index']);
            Route::patch('/{id}/payment_requests', ['as' => 'payment_requests', 'uses' => 'Guest\PaymentRequestController@payment']);
            Route::get('/{id}/payment_requests', ['as' => 'get_payment_requests',
                'uses' => 'Guest\PaymentRequestController@getPaymentRequest']);
        });

        Route::group(['prefix' => 'point_settement', 'as' => 'point_settement.'], function () {
            Route::post('/{orderId}', ['as' => 'create', 'uses' => 'Guest\OrderController@pointSettlement']);
        });
    });

    Route::group(['middleware' => ['auth:api', 'cast'], 'prefix' => 'cast', 'as' => 'cast.'], function () {
        Route::get('/orders', ['as' => 'index', 'uses' => 'Cast\OrderController@index']);
        Route::get('/payment_requests', ['as' => 'get_payment_history', 'uses' => 'Cast\PaymentRequestController@getPaymentHistory']);
        Route::delete('/order/{id}', ['as' => 'index', 'uses' => 'Cast\OrderController@delete']);
        Route::get('/payments', ['as' => 'payments', 'uses' => 'Cast\PaymentController@payments']);
        Route::patch('/confirm_transfer', ['as' => 'confirm_transfer', 'uses' => 'Cast\CastController@confirmTransfer']);

        Route::get('/points', ['as' => 'points', 'uses' => 'Cast\PointController@points']);

        Route::group(['prefix' => 'bank_accounts', 'as' => 'bank_accounts.'], function () {
            Route::post('/{id}', ['as' => 'update', 'uses' => 'Cast\BankAccountController@update']);
            Route::post('/', ['as' => 'bank_accounts', 'uses' => 'Cast\BankAccountController@create']);
        });
    });

    Route::group(['middleware' => ['auth:api'], 'prefix' => 'orders', 'as' => 'orders.'], function () {
        Route::post('/', ['as' => 'create', 'uses' => 'OrderController@create']);
        Route::get('/{id}', ['as' => 'show', 'uses' => 'OrderController@show']);
        Route::post('/price', ['as' => 'price', 'uses' => 'OrderController@price']);
        Route::post('/create_offer', ['as' => 'create_offer', 'uses' => 'OrderController@createOrderOffer']);
    });

    Route::group(['middleware' => ['auth:api', 'cast']], function () {
        Route::group(['prefix' => 'orders', 'as' => 'orders.'], function () {
            Route::post('/{id}/deny', ['as' => 'deny', 'uses' => 'Cast\OrderController@deny'])
                ->where('id', '[0-9]+');
            Route::post('/{id}/apply', ['as' => 'apply', 'uses' => 'Cast\OrderController@apply'])
                ->where('id', '[0-9]+');
            Route::post('/{id}/accept', ['as' => 'accept', 'uses' => 'Cast\OrderController@accept'])
                ->where('id', '[0-9]+');
            Route::post('/{id}/stop', ['as' => 'stop', 'uses' => 'Cast\OrderController@stop'])
                ->where('id', '[0-9]+');
            Route::post('/{id}/start', ['as' => 'start', 'uses' => 'Cast\OrderController@start'])
                ->where('id', '[0-9]+');
            Route::post('/{id}/thank', ['as' => 'thanks', 'uses' => 'Cast\OrderController@thanks'])
                ->where('id', '[0-9]+');
            Route::post('/{id}/payment_request', ['as' => 'payment_request',
                'uses' => 'Cast\PaymentRequestController@createPayment'])->where('id', '[0-9]+');
            Route::get('/{id}/payment_request', ['as' => 'get_payment_request', 'uses' => 'Cast\PaymentRequestController@payment'])
                ->where('id', '[0-9]+');
        });
    });

    Route::group(['middleware' => ['auth:api', 'guest']], function () {
        Route::group(['prefix' => 'orders', 'as' => 'orders.'], function () {
            Route::post('/{id}/cancel', ['as' => 'cancel', 'uses' => 'Guest\OrderController@cancel'])
                ->where('id', '[0-9]+');
        });

        Route::group(['prefix' => 'cards', 'as' => 'cards.'], function () {
            Route::get('/', ['as' => 'index', 'uses' => 'CardController@index']);
            Route::post('/', ['as' => 'create', 'uses' => 'CardController@create']);
            Route::delete('{id}', ['as' => 'delete', 'uses' => 'CardController@destroy']);
        });

        Route::group(['prefix' => 'points', 'as' => 'points.'], function () {
            Route::get('/', ['as' => 'points', 'uses' => 'PointController@points']);
            Route::post('/', ['as' => 'buy', 'uses' => 'Guest\PointController@buy']);
        });

        Route::group(['prefix' => 'coupons', 'as' => 'coupons.'], function () {
            Route::get('/', ['as' => 'index', 'uses' => 'Guest\CouponController@getCoupons']);
        });
    });

    Route::group(['middleware' => ['auth:api'], 'prefix' => 'receipts', 'as' => 'receipts.'], function () {
        Route::post('/', ['as' => 'create', 'uses' => 'ReceiptController@create']);
    });
});
