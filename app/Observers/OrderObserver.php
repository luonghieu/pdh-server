<?php

namespace App\Observers;

use App\Enums\OrderType;
use App\Notifications\CreateNominatedOrdersForCast;
use App\Notifications\CreateNominatedOrdersForGuest;
use App\Order;

class OrderObserver
{
    public function created(Order $order)
    {
        if (OrderType::NOMINATED_CALL == $order->type || OrderType::CALL == $order->type) {
            $nominees = $order->nominees;
            $order->user->notify(new CreateNominatedOrdersForGuest($order));
            if (count($nominees)) {
                \Notification::send($nominees, new CreateNominatedOrdersForCast($order));
            }
        }
    }
}
