<?php

namespace App\Observers;

use App\Enums\OrderType;
use App\Notifications\CreateNominatedOrdersForGuest;
use App\Order;

class OrderObserver
{
    public function created(Order $order)
    {
        if ($order->type == OrderType::NOMINATED_CALL || $order->type == OrderType::CALL) {
            $order->user->notify(new CreateNominatedOrdersForGuest($order));
        }
    }
}
