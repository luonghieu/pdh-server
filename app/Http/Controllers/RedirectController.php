<?php

namespace App\Http\Controllers;

use App\Enums\OrderPaymentStatus;
use App\Order;
use Illuminate\Http\Request;

class RedirectController extends Controller
{
    public function index(Request $request)
    {
        $page = $request->page;

        switch ($page) {
            case 'call':
                return \Redirect::to(route('guest.orders.call'));
            case 'room':
                return \Redirect::to(route('message.messages', ['room' => $request->room_id]));
            case 'evaluation':
                $order = Order::find($request->order_id);
                if ($order->payment_status == OrderPaymentStatus::PAYMENT_FINISHED) {
                    return \Redirect::to(route('history.show', ['orderId' => $request->order_id]));
                }

                return \Redirect::to(route('evaluation.index', ['order_id' => $request->order_id]));
            case 'message':
                return \Redirect::to(route('message.index'));
            case 'credit_card':
                \Session::put('order_history', $request->order_id);
                return \Redirect::to(route('credit_card.update'));
            default:
                return \Redirect::to(route('web.index'));
        }
    }
}
