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

            $order = null;
            $orders = Order::where('user_id', Auth::user()->id)
                ->whereIn('status', [OrderStatus::OPEN, OrderStatus::ACTIVE, OrderStatus::PROCESSING])
                ->orderBy('created_at');

            if ($orders) {
                if ($order = $orders->where('status', OrderStatus::PROCESSING)) {
                    $order = $order->first();
                } else {
                    $order = $orders->first();
                }
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
