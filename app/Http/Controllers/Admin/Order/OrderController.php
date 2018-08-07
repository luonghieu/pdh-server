<?php

namespace App\Http\Controllers\Admin\Order;

use App\Http\Controllers\Controller;
use App\Order;
use Carbon\Carbon;
use Illuminate\Http\Request;

class OrderController extends Controller
{

    public function index(Request $request)
    {
        $keyword = $request->search;

        $orders = Order::with('user');

        if ($request->has('from_date') && !empty($request->from_date)) {
            $fromDate = Carbon::parse($request->from_date)->startOfDay();
            $orders->where(function ($query) use ($fromDate) {
                $query->whereDate('date', '>=', $fromDate);
            });
        }

        if ($request->has('to_date') && !empty($request->to_date)) {
            $toDate = Carbon::parse($request->to_date)->endOfDay();
            $orders->where(function ($query) use ($toDate) {
                $query->whereDate('date', '<=', $toDate);
            });
        }

        if ($request->has('search')) {
            $orders->where('id', $keyword)
                ->orWhereHas('user', function ($query) use ($keyword) {
                    $query->where('id', "$keyword")
                        ->orWhere('nickname', 'like', "%$keyword%");
                });
        }

        $orders = $orders->orderBy('created_at', 'DESC')->paginate($request->limit ?: 10);

        return view('admin.orders.index', compact('orders'));
    }

    public function deleteOrder(Request $request)
    {
        if ($request->has('order_ids')) {
            $orderIds = $request->order_ids;

            $checkOrderIdExist = Order::whereIn('id', $orderIds)->exists();

            if ($checkOrderIdExist) {
                Order::whereIn('id', $orderIds)->delete();
            }
        }

        return redirect(route('admin.orders.index'));
    }

    public function nominees(Order $order)
    {
        $casts = $order->nominees()->paginate();

        return view('admin.orders.nominees', compact('casts', 'order'));
    }

    public function candidates(Order $order)
    {
        $casts = $order->candidates()->paginate();

        return view('admin.orders.candidates', compact('casts', 'order'));
    }

    public function orderCall(Order $order)
    {
        $order = $order->load('candidates', 'nominees', 'user', 'castClass', 'room', 'casts', 'tags');

        return view('admin.orders.order_call', compact('order'));
    }
}
