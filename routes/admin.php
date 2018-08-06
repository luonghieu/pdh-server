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
        Route::put('{user}/register_guest', ['as' => 'register_guest', 'uses' => 'UserController@registerGuest']);
        Route::get('{user}/orders', ['as' => 'orders_history', 'uses' => 'OrderController@getOrderHistory'])->where('user', '[0-9]+');
        Route::get('{user}/points', ['as' => 'points_history', 'uses' => 'PointController@getPointHistory'])->where('user', '[0-9]+');;
        Route::put('{user}/points', ['as' => 'change_point', 'uses' => 'PointController@changePoint'])->where('user', '[0-9]+');;
        Route::get('{user}/cast_ratings', ['as' => 'cast_ratings', 'uses' => 'RatingController@ratings'])->where('user', '[0-9]+');
    });

    Route::group(['namespace' => 'Cast', 'prefix' => 'casts', 'as' => 'casts.', 'middleware' => 'is_admin'], function () {
        Route::get('/', ['as' => 'index', 'uses' => 'CastController@index']);
        Route::get('{user}/register', ['as' => 'register', 'uses' => 'CastController@registerCast']);
        Route::post('{user}/confirm', ['as' => 'confirm', 'uses' => 'CastController@confirmRegister']);
        Route::post('{user}/save', ['as' => 'save', 'uses' => 'CastController@saveCast']);
        Route::get('{user}/guest_ratings', ['as' => 'guest_ratings', 'uses' => 'RatingController@ratings'])->where('user', '[0-9]+');
    });

    Route::group(['middleware' => 'is_admin'], function () {
        Route::get('cast_rankings', ['as' => 'cast_rankings.index', 'uses' => 'CastRankingController@index']);
    });

    Route::group(['namespace' => 'Room', 'prefix' => 'rooms', 'as' => 'rooms.', 'middleware' => 'is_admin'], function () {
        Route::get('{room}/messages', ['as' => 'messages_by_room', 'uses' => 'RoomController@getMessageByRoom'])->where('room', '[0-9]+');
        Route::put('{room}', ['as' => 'change_active', 'uses' => 'RoomController@changeActive'])->where('room', '[0-9]+');
        Route::get('/', ['as' => 'index', 'uses' => 'RoomController@index']);
        Route::get('/{room}/members', ['as' => 'members', 'uses' => 'RoomController@getMember']);
    });

    Route::group(['namespace' => 'Order', 'prefix' => 'orders', 'as' => 'orders.', 'middleware' => 'is_admin'], function () {
        Route::get('/', ['as' => 'index', 'uses' => 'OrderController@index']);
        Route::get('{order}/candidates', ['as' => 'candidates', 'uses' => 'OrderController@candidates'])->where('order', '[0-9]+');
        Route::get('/{order}/nominees', ['as' => 'nominees', 'uses' => 'OrderController@nominees'])->where('order', '[0-9]+');
        Route::get('{order}', ['as' => 'call', 'uses' => 'OrderController@orderCall'])->where('order', '[0-9]+');
    });

    Route::group(['namespace' => 'Chat_Room', 'prefix' => 'chat_rooms', 'as' => 'chat_rooms.', 'middleware' => 'is_admin'], function () {
        Route::get('/', ['as' => 'index', 'uses' => 'ChatRoomController@index']);
    });


});
