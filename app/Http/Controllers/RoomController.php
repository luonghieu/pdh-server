<?php

namespace App\Http\Controllers;

use Auth;
use GuzzleHttp\Client;
use JWTAuth;

class RoomController extends Controller
{
    public function index()
    {
        $accessToken = JWTAuth::fromUser(Auth::user());
        $client = new Client(['base_uri' => config('common.api_url')]);
        $option = [
            'headers' => ['Authorization' => 'Bearer ' . $accessToken],
            'form_params' => [],
            'allow_redirects' => false,
        ];

        $response = $client->get(route('rooms.room'), $option);
        $contents = json_decode($response->getBody()->getContents());
        $rooms = $contents->data;

        return view('web.rooms.rooms', compact('rooms'));
    }
}
