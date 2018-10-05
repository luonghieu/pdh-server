<?php

namespace App\Http\Controllers;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Order;
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
                ->orderBy('date')
                ->orderBy('start_time')->first();

            if (!$order) {
                $order = Order::with('casts')
                    ->where('user_id', Auth::user()->id)
                    ->whereIn('status', [OrderStatus::OPEN, OrderStatus::ACTIVE])
                    ->orderBy('date')
                    ->orderBy('start_time')->first();
            }

            return view('web.index', compact('token', 'order'));
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
