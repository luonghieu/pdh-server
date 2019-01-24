<?php

namespace App\Http\Controllers;

use Auth;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use JWTAuth;

class CardSquareController extends Controller
{
    public function create()
    {
        return view('web.cards_square.create');
    }

    public function createCard(Request $request)
    {
        $accessToken = JWTAuth::fromUser(Auth::user());
        $client = new Client(['base_uri' => config('common.api_url')]);
        $param = $request->nonce;

        $option = [
            'headers' => ['Authorization' => 'Bearer ' . $accessToken],
            'form_params' => ['token' => $param],
            'allow_redirects' => false,
        ];

        // $response = $client->get(route('cards.create'), $option);
        $response = $client->post('/api/v1/cards', $option);
        $getContents = json_decode($response->getBody()->getContents(), JSON_NUMERIC_CHECK);

    }
}
