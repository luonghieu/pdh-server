<?php

namespace App\Http\Controllers;

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
                return \Redirect::to(route('evaluation.index', ['order_id' => $request->order_id]));
            case 'message':
                return \Redirect::to(route('message.index'));
            default:
                return \Redirect::to(route('web.index'));
        }
    }
}
