<?php

namespace App\Http\Controllers;

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
            return redirect()->route('message.messages', ['room' => $order->room_id]);
        }

        $totalRated = 1;
        if ($order->total_cast > 1) {
            $casts = $order->casts;
            foreach ($casts as $cast) {
                if ($cast->pivot->guest_rated) {
                    $totalRated++;
                }
            }
        } else {
            $totalRated = -1;
        }


        return view('web.ratings.index', compact(['order', 'cast', 'totalRated']));
    }
}
