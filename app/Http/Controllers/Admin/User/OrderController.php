<?php

namespace App\Http\Controllers\Admin\User;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\User;

class OrderController extends Controller
{
    public function getOrderHistory(User $user)
    {
        $orders = $user->orders()->with('casts')->where('status', OrderStatus::DONE)->latest()->paginate();

        return view('admin.users.orders_history', compact('orders', 'user'));
    }
}
