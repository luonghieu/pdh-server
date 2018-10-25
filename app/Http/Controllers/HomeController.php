<?php

namespace App\Http\Controllers;

use App\Enums\OrderStatus;
use App\Enums\UserType;
use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use App\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Session;
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
            $user = Auth::user();
            $token = '';
            $token = JWTAuth::fromUser($user);

            if (UserType::GUEST == $user->type) {
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

                return view('web.index', compact('token', 'order'));
            }

            if (UserType::CAST == $user->type) {
                return view('web.cast.index', compact('token', 'user'));
            }
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
