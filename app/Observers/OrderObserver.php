<?php

namespace App\Observers;

use App\Enums\OrderPaymentStatus;
use App\Enums\OrderType;
use App\Notifications\CompletedPayment;
use App\Notifications\CreateNominatedOrdersForCast;
use App\Notifications\CreateNominatedOrdersForGuest;
use App\Order;

class OrderObserver
{
    public function created(Order $order)
    {
        if (OrderType::NOMINATED_CALL == $order->type || OrderType::CALL == $order->type) {
            $order->user->notify(new CreateNominatedOrdersForGuest($order));
//            $nominees = $order->nominees;
//            if (count($nominees)) {
//                \Notification::send($nominees, new CreateNominatedOrdersForCast($order));
//            }
        }
    }

    public function updated(Order $order)
    {
        if ($order->getOriginal('payment_status') != $order->payment_status) {
            if ($order->payment_status == OrderPaymentStatus::PAYMENT_FINISHED) {
                $order->user->notify(new CompletedPayment($order));
            }
        }
    }
}
