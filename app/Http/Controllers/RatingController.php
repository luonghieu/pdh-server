<?php

namespace App\Http\Controllers;

use App\Cast;
use App\Order;
use Illuminate\Http\Request;

class RatingController extends Controller
{
    public function index(Request $request)
    {
        if (!isset($request->order_id) && !isset($request->cast_id)) {
            return redirect()->back();
        }

        $cast = Cast::find($request->cast_id);
        $order = Order::find($request->order_id);

        if (!$order || !$cast) {
            return redirect()->back();
        }

        return view('web.ratings.index', compact(['order', 'cast']));
    }
}
