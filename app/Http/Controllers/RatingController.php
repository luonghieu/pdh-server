<?php

namespace App\Http\Controllers;

use App\Cast;
use App\Enums\OrderStatus;
use App\Order;
use Illuminate\Http\Request;

class RatingController extends Controller
{
    public function index(Request $request)
    {
        $order = Order::where('status', OrderStatus::DONE)->find($request->order_id);
        if (!$order) {
            return redirect()->back();
        }

        $cast = $order->casts()->wherePivot('guest_rated', false)->first();

        if (!$cast) {
            return redirect()->back();
        }

        return view('web.ratings.index', compact(['order', 'cast']));
    }
}
