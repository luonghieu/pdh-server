<?php

Route::redirect('/admin', '/admin/login', 301);
Route::group(['prefix' => 'admin', 'as' => 'admin.'], function () {
    Route::get('login', ['as' => 'login', 'uses' => 'AuthController@index']);
    Route::post('login', ['as' => 'login', 'uses' => 'AuthController@login']);
    Route::get('logout', ['as' => 'logout', 'uses' => 'AuthController@logout']);
    Route::group(['namespace' => 'User', 'prefix' => 'users', 'as' => 'users.', 'middleware' => 'is_admin'], function () {
        Route::get('/', ['as' => 'index', 'uses' => 'UserController@index']);
        Route::get('{user}', ['as' => 'show', 'uses' => 'UserController@show'])->where('user', '[0-9]+');
        Route::put('{user}', ['as' => 'change_active', 'uses' => 'UserController@changeActive'])->where('user', '[0-9]+');
        Route::post('{user}', ['as' => 'change_cast_class', 'uses' => 'UserController@changeCastClass'])->where('user', '[0-9]+');
        Route::put('{user}/register_guest', ['as' => 'register_guest', 'uses' => 'UserController@registerGuest']);
        Route::get('{user}/orders', ['as' => 'orders_history', 'uses' => 'OrderController@getOrderHistory'])->where('user', '[0-9]+');
        Route::get('{user}/points', ['as' => 'points_history', 'uses' => 'PointController@getPointHistory'])->where('user', '[0-9]+');
        Route::put('{user}/points', ['as' => 'change_point', 'uses' => 'PointController@changePoint'])->where('user', '[0-9]+');
        Route::get('{user}/cast_ratings', ['as' => 'cast_ratings', 'uses' => 'RatingController@ratings'])->where('user', '[0-9]+');
        Route::post('{user}/change_prefecture', ['as' => 'change_prefecture', 'uses' => 'UserController@changePrefecture'])->where('user', '[0-9]+');
        Route::post('{user}/change_cost', ['as' => 'change_cost', 'uses' => 'UserController@changeCost'])->where('user', '[0-9]+');
        Route::post('{user}/change_rank', ['as' => 'change_rank', 'uses' => 'UserController@changeRank'])->where('user', '[0-9]+');
        Route::post('{user}/campaign_participated', ['as' => 'campaign_participated', 'uses' => 'UserController@campaignParticipated'])->where('user', '[0-9]+');
        Route::post('{user}/change_payment_method', ['as' => 'change_payment_method', 'uses' => 'UserController@changePaymentMethod'])->where('user', '[0-9]+');
        Route::delete('{user}', ['as' => 'delete', 'uses' => 'UserController@delete'])->where('user', '[0-9]+');
    });

    Route::group(['as' => 'avatars.', 'middleware' => 'is_admin'], function () {
        Route::post('{user}/avatars', ['as' => 'upload', 'uses' => 'AvatarController@upload'])->where('user', '[0-9]+');
        Route::post('{user}/avatars/{id}', ['as' => 'update', 'uses' => 'AvatarController@update']);
        Route::patch('{user}/avatars/{id}', ['as' => 'set_avatar_default', 'uses' => 'AvatarController@setAvatarDefault']);
        Route::delete('{user}/avatars/{id}', ['as' => 'delete', 'uses' => 'AvatarController@delete']);
    });

    Route::group(['namespace' => 'RequestTransfer', 'prefix' => 'request_transfer', 'as' => 'request_transfer.', 'middleware' => 'is_admin'], function () {
        Route::get('/', ['as' => 'index', 'uses' => 'RequestTransferController@index']);
        Route::get('{cast}', ['as' => 'show', 'uses' => 'RequestTransferController@show'])->where('cast', '[0-9]+');
        Route::put('{cast}', ['as' => 'update', 'uses' => 'RequestTransferController@update'])->where('cast', '[0-9]+');
    });

    Route::group(['namespace' => 'Cast', 'prefix' => 'casts', 'as' => 'casts.', 'middleware' => 'is_admin'], function () {
        Route::get('/', ['as' => 'index', 'uses' => 'CastController@index']);
        Route::get('create', ['as' => 'create', 'uses' => 'CastController@create']);
        Route::post('/', ['as' => 'store', 'uses' => 'CastController@store']);
        Route::get('{user}/register', ['as' => 'register', 'uses' => 'CastController@registerCast']);
        Route::post('{user}/confirm', ['as' => 'confirm', 'uses' => 'CastController@confirmRegister']);
        Route::post('{user}/save', ['as' => 'save', 'uses' => 'CastController@saveCast']);
        Route::post('{user}/change_status_work', ['as' => 'change_status_work', 'uses' => 'CastController@changeStatusWork'])->where('user', '[0-9]+');
        Route::get('{user}/guest_ratings', ['as' => 'guest_ratings', 'uses' => 'RatingController@ratings'])->where('user', '[0-9]+');
        Route::get('{user}/operation_history', ['as' => 'operation_history', 'uses' => 'CastController@getOperationHistory'])->where('user', '[0-9]+');
        Route::put('{user}/operation_history', ['as' => 'change_point', 'uses' => 'CastController@changePoint'])->where('user', '[0-9]+');
        Route::get('/export_bank_accounts', ['as' => 'export_bank_accounts', 'uses' => 'CastController@exportBankAccounts']);
        Route::get('/{user}/bank_account', ['as' => 'bank_account', 'uses' => 'CastController@bankAccount'])->where('user', '[0-9]+');
        Route::put('/{user}/update_note', ['as' => 'update_note', 'uses' => 'CastController@updateNote'])->where('user', '[0-9]+');
        Route::put('/{user}/update_cost_rate', ['as' => 'update_cost_rate', 'uses' => 'CastController@updateCostRate'])->where('user', '[0-9]+');
        Route::get('/{user}/schedule', ['as' => 'schedule', 'uses' => 'ShiftController@index'])->where('user', '[0-9]+');
    });

    Route::group(['middleware' => 'is_admin'], function () {
        Route::get('cast_rankings', ['as' => 'cast_rankings.index', 'uses' => 'CastRankingController@index']);

        Route::group(['prefix' => 'rank_schedules', 'as' => 'rank_schedules.'], function () {
            Route::get('/', ['as' => 'index', 'uses' => 'RankScheduleController@getRankSchedule']);
            Route::put('/', ['as' => 'update', 'uses' => 'RankScheduleController@setRankSchedule']);
            Route::get('/casts', ['as' => 'casts', 'uses' => 'RankScheduleController@getListCast']);
        });
    });

    Route::group(['namespace' => 'Room', 'prefix' => 'rooms', 'as' => 'rooms.', 'middleware' => 'is_admin'], function () {
        Route::get('{room}/messages', ['as' => 'messages_by_room', 'uses' => 'RoomController@getMessageByRoom'])->where('room', '[0-9]+');
        Route::put('{room}', ['as' => 'change_active', 'uses' => 'RoomController@changeActive'])->where('room', '[0-9]+');
        Route::get('/', ['as' => 'index', 'uses' => 'RoomController@index']);
        Route::get('/{room}/members', ['as' => 'members', 'uses' => 'RoomController@getMember']);
    });

    Route::group(['namespace' => 'Order', 'prefix' => 'orders', 'as' => 'orders.', 'middleware' => 'is_admin'], function () {
        Route::get('/', ['as' => 'index', 'uses' => 'OrderController@index']);
        Route::delete('/', ['as' => 'delete', 'uses' => 'OrderController@deleteOrder']);
        Route::get('{order}/candidates', ['as' => 'candidates', 'uses' => 'OrderController@candidates'])->where('order', '[0-9]+');
        Route::get('/{order}/nominees', ['as' => 'nominees', 'uses' => 'OrderController@nominees'])->where('order', '[0-9]+');
        Route::get('{order}', ['as' => 'call', 'uses' => 'OrderController@orderCall'])->where('order', '[0-9]+');
        Route::get('{order}/edit', ['as' => 'edit_order_call', 'uses' => 'OrderController@editOrderCall'])->where('order', '[0-9]+');
        Route::get('{order}/order_nominee', ['as' => 'order_nominee', 'uses' => 'OrderController@orderNominee'])->where('order', '[0-9]+');
        Route::get('{order}/casts_matching', ['as' => 'casts_matching', 'uses' => 'OrderController@castsMatching'])->where('order', '[0-9]+');
        Route::put('change_start_time_order_call', ['as' => 'change_start_time_order_call', 'uses' => 'OrderController@changeStartTimeOrderCall']);
        Route::put('change_stopped_time_order_call', ['as' => 'change_stopped_time_order_call', 'uses' => 'OrderController@changeStopTimeOrderCall']);
        Route::put('{order}/change_payment_request_status', ['as' => 'change_payment_request_status', 'uses' => 'OrderController@changePaymentRequestStatus'])->where('order', '[0-9]+');
        Route::put('{order}/point_settlement', ['as' => 'point_settlement', 'uses' => 'OrderController@pointSettlement'])->where('order', '[0-9]+');
        Route::put('change_start_time_order_nominee', ['as' => 'change_start_time_order_nominee', 'uses' => 'OrderController@changeStartTimeOrderNominee']);
        Route::put('change_stop_time_order_nominee', ['as' => 'change_stop_time_order_nominee', 'uses' => 'OrderController@changeStopTimeOrderNominee']);
        Route::get('/casts/{classId}', ['as' => 'get_cast_by_classid', 'uses' => 'OrderController@getCasts']);
        Route::put('/{id}', ['as' => 'get_cast_by_classid', 'uses' => 'OrderController@updateOrderCall']);
        Route::get('/list_guests', ['as' => 'get_guest_by_device_type', 'uses' => 'OrderController@getListGuests']);
    });

    Route::group(['middleware' => 'is_admin'], function () {
        Route::get('chat', ['as' => 'chat.index', 'uses' => 'ChatRoomController@index']);
    });

    Route::group(['namespace' => 'Report', 'prefix' => 'reports', 'as' => 'reports.', 'middleware' => 'is_admin'], function () {
        Route::get('/', ['as' => 'index', 'uses' => 'ReportController@index']);
        Route::put('/', ['as' => 'make_report_done', 'uses' => 'ReportController@makeReportDone']);
    });

    Route::group(['namespace' => 'Point', 'prefix' => 'points', 'as' => 'points.', 'middleware' => 'is_admin'], function () {
        Route::get('/point_users', ['as' => 'point_users', 'uses' => 'PointController@getPointUser']);
        Route::get('/', ['as' => 'index', 'uses' => 'PointController@index']);
        Route::get('/transaction_history', ['as' => 'transaction_history', 'uses' => 'PointController@getTransactionHistory']);
    });

    Route::group(['namespace' => 'Sales', 'prefix' => 'sales', 'as' => 'sales.', 'middleware' => 'is_admin'], function () {
        Route::get('/', ['as' => 'index', 'uses' => 'SaleController@index']);
    });

    Route::group(['namespace' => 'Transfer', 'prefix' => 'transfers', 'as' => 'transfers.', 'middleware' => 'is_admin'], function () {
        Route::get('/non_transfers', ['as' => 'non_transfers', 'uses' => 'TransferController@getNotTransferedList']);
        Route::get('/transfered', ['as' => 'transfered', 'uses' => 'TransferController@getTransferedList']);
        Route::post('/change_transfers', ['as' => 'change_transfers', 'uses' => 'TransferController@changeTransfers']);
    });

    Route::group(['prefix' => 'notifications', 'as' => 'notifications.', 'middleware' => 'is_admin'], function () {
        Route::post('/make_read', ['as' => 'make_read', 'uses' => 'NotificationController@makeAsRead']);
    });

    Route::group(['namespace' => 'NotificationSchedule', 'prefix' => 'notification_schedules', 'as' => 'notification_schedules.', 'middleware' => 'is_admin'], function () {
        Route::get('/create', ['as' => 'create', 'uses' => 'NotificationScheduleController@create']);
        Route::post('/', ['as' => 'store', 'uses' => 'NotificationScheduleController@store']);
        Route::get('/{notification_schedule}/edit', ['as' => 'edit', 'uses' => 'NotificationScheduleController@edit'])->where('notification_schedules', '[0-9]+');
        Route::put('/{notification_schedule}', ['as' => 'update', 'uses' => 'NotificationScheduleController@update'])->where('notification_schedules', '[0-9]+');
        Route::delete('/{notification_schedule}', ['as' => 'delete', 'uses' => 'NotificationScheduleController@delete'])->where('notification_schedules', '[0-9]+');
        Route::get('/', ['as' => 'index', 'uses' => 'NotificationScheduleController@getNotificationScheduleList']);
        Route::post('/upload', ['as' => 'upload', 'uses' => 'UploadImageController@upload']);
    });

    Route::group(['prefix' => 'verifications', 'as' => 'verifications.', 'middleware' => 'is_admin'], function () {
        Route::get('/', ['as' => 'index', 'uses' => 'VerificationController@index']);
    });

    Route::group(['prefix' => 'favorites', 'as' => 'favorites.', 'middleware' => 'is_admin'], function () {
        Route::get('/guest', ['as' => 'guest', 'uses' => 'FavoriteController@guest']);
        Route::get('/cast', ['as' => 'cast', 'uses' => 'FavoriteController@cast']);
    });

    Route::group(['namespace' => 'Offer', 'prefix' => 'offers', 'as' => 'offers.', 'middleware' => 'is_admin'], function () {
        Route::get('/', ['as' => 'index', 'uses' => 'OfferController@index']);
        Route::get('/create', ['as' => 'create', 'uses' => 'OfferController@create']);
        Route::post('/', ['as' => 'store', 'uses' => 'OfferController@store']);
        Route::post('/confirm', ['as' => 'confirm', 'uses' => 'OfferController@confirm']);
        Route::post('/price', ['as' => 'price', 'uses' => 'OfferController@price']);
        Route::get('/{offer}', ['as' => 'detail', 'uses' => 'OfferController@detail'])->where('offer', '[0-9]+');
        Route::delete('/{offer}', ['as' => 'delete', 'uses' => 'OfferController@delete'])->where('offer', '[0-9]+');
        Route::get('/edit/{offer}', ['as' => 'edit', 'uses' => 'OfferController@edit'])->where('offer', '[0-9]+');
    });

    Route::group(['namespace' => 'Coupon', 'prefix' => 'coupons', 'as' => 'coupons.', 'middleware' => 'is_admin'], function () {
        Route::get('/', ['as' => 'index', 'uses' => 'CouponController@index']);
        Route::get('/create', ['as' => 'create', 'uses' => 'CouponController@create']);
        Route::get('/{coupon}', ['as' => 'show', 'uses' => 'CouponController@show'])->where('coupon', '[0-9]+');
        Route::post('/create', ['as' => 'store', 'uses' => 'CouponController@store']);
        Route::get('/delete/{coupon}', ['as' => 'delete', 'uses' => 'CouponController@delete'])->where('coupon', '[0-9]+');
        Route::post('/edit/{coupon}', ['as' => 'update', 'uses' => 'CouponController@update']);
        Route::get('/history/{coupon}', ['as' => 'history', 'uses' => 'CouponController@history'])->where('coupon', '[0-9]+');
        Route::put('/update_sort_index', ['as' => 'update_sort_index', 'uses' => 'CouponController@updateSortIndex']);
        Route::put('/update_is_active', ['as' => 'update_is_active', 'uses' => 'CouponController@updateIsActive'])->where('coupon', '[0-9]+');
    });

    Route::group(['prefix' => 'app_versions', 'as' => 'app_versions.', 'middleware' => 'is_admin'], function () {
        Route::get('/', ['as' => 'index', 'uses' => 'AppVersionController@index']);
        Route::put('/{app_version}', ['as' => 'update', 'uses' => 'AppVersionController@update'])->where('app_version', '[0-9]+');
    });

    Route::group(['namespace' => 'CostEnterprise', 'prefix' => 'cost_enterprises', 'as' => 'cost_enterprises.', 'middleware' => 'is_admin'], function () {
        Route::get('/', ['as' => 'index', 'uses' => 'CostEnterpriseController@index']);
    });

    Route::group(['namespace' => 'InviteCodeHistory', 'prefix' => 'invite_code_histories', 'as' => 'invite_code_histories.', 'middleware' => 'is_admin'], function () {
        Route::get('/', ['as' => 'index', 'uses' => 'InviteCodeHistoryController@index']);
        Route::get('/{invite_code_history}', ['as' => 'show', 'uses' => 'InviteCodeHistoryController@show'])->where('invite_code_history', '[0-9]+');
    });

    Route::group(['namespace' => 'Timeline', 'prefix' => 'timelines', 'as' => 'timelines.', 'middleware' => 'is_admin'], function () {
        Route::get('/', ['as' => 'index', 'uses' => 'TimelineController@index']);
        Route::post('/{timeline}/change_status_hidden', ['as' => 'change_status_hidden', 'uses' => 'TimelineController@changeStatusHidden'])->where('timeline', '[0-9]+');
    });
});
