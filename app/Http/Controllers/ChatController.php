<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Message;
use App\Room;
use Auth;
use GuzzleHttp\Client;
use JWTAuth;

class ChatController extends Controller
{
    public function message(Room $room)
    {
        $accessToken = JWTAuth::fromUser(Auth::user());
        $client = new Client();
        $option = [
            'headers' => ['Authorization' => 'Bearer ' . $accessToken],
            'form_params' => [],
            'allow_redirects' => false,
        ];

        $response = $client->get(route('rooms.index', ['id' => $room->id]), $option);
        $getContents = json_decode($response->getBody()->getContents(), JSON_NUMERIC_CHECK);
        $messages = $getContents['data'];

        return view('web.chat', compact('room', 'accessToken', 'messages'));
    }
}
