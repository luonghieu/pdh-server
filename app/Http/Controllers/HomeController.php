<?php

namespace App\Http\Controllers;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use App\Order;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Session;
use File;
use Tymon\JWTAuth\Facades\JWTAuth;

class HomeController extends Controller
{
    public function ld()
    {
        return view('web.ld');
    }

    public function index(Request $request)
    {
        if ($request->session()->has('data')) {
            $request->session()->forget('data');
        }

        if (Auth::check()) {
            $token = '';
            $token = JWTAuth::fromUser(Auth::user());

            $order = Order::with('casts')
                ->where('user_id', Auth::user()->id)
                ->where('status', OrderStatus::PROCESSING)
                ->with(['user', 'casts', 'nominees', 'tags'])
                ->orderBy('date')
                ->orderBy('start_time')->first();

            if (!$order) {
                $order = Order::with('casts')
                    ->where('user_id', Auth::user()->id)
                    ->whereIn('status', [OrderStatus::OPEN, OrderStatus::ACTIVE])
                    ->with(['user', 'casts', 'nominees', 'tags'])
                    ->orderBy('date')
                    ->orderBy('start_time')->first();
            }

            $order = OrderResource::make($order);

            $client = new Client(['base_uri' => config('common.api_url')]);
            $option = [
                'headers' => ['Authorization' => 'Bearer ' . $token],
                'form_params' => [],
                'allow_redirects' => false,
            ];

            $response = $client->get(route('casts.index', ['working_today' => 1, 'device' => 3]), $option);
            $getContents = json_decode($response->getBody()->getContents());
            $casts = $getContents->data;

            return view('web.index', compact('token', 'order', 'casts'));
        }

        return redirect()->route('web.login');
    }

    public function login()
    {
        if (Auth::check()) {
            return redirect()->route('web.index');
        }

        return view('web.login');
    }

    public function logout()
    {
        Auth::logout();

        return redirect()->route('web.index');
    }
}
