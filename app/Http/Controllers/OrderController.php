<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Auth;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use JWTAuth;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $client = new Client();
        $user = Auth::user();

        $accessToken = JWTAuth::fromUser($user);

        $option = [
            'headers' => ['Authorization' => 'Bearer ' . $accessToken],
            'form_params' => [],
            'allow_redirects' => false,
        ];

        $response = $client->get(route('guest.index'), $option);

        $result = $response->getBody();
        $contents = $result->getContents();
        $contents = json_decode($contents, JSON_NUMERIC_CHECK);
        $orders = $contents['data'];

        return view('web.orders.list', compact('orders'));
    }

    public function cancel(Request $request)
    {
        $id = $request->id;

        $client = new Client();
        $user = Auth::user();

        $accessToken = JWTAuth::fromUser($user);

        $option = [
            'headers' => ['Authorization' => 'Bearer ' . $accessToken],
            'form_params' => [],
            'allow_redirects' => false,
        ];

        $response = $client->post(route('orders.cancel', ['id' => $id]), $option);

        if ($response->getStatusCode() == 200) {
            return response()->json(['status' => true]);
        }

        return response()->json(['status' => false]);
    }
}
