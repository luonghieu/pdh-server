<?php

namespace App\Http\Controllers;

use App\Cast;
use App\Order;
use App\Http\Controllers\Controller;
use App\Enums\OrderStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        if (Auth::check()) {
            $token = '';
            $token = JWTAuth::fromUser(Auth::user());

            $orders = Order::where('user_id', Auth::user()->id)->orderBy('created_at', 'DESC');

            if ($order = $orders->where('type', OrderStatus::PROCESSING)) {
                $order = $order->first();
            } else {
                $order = $orders->first();
            }

            return view('web.index', compact('token', 'order'));
        }

        return view('web.login');
    }

    public function logout()
    {
        Auth::logout();

        return redirect()->route('web.index');
    }
}
