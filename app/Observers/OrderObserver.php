<?php

namespace App\Observers;

use App\Enums\OrderPaymentStatus;
use App\Enums\OrderType;
use App\Enums\ProviderType;
use App\Notifications\CompletedPayment;
use App\Notifications\CreateNominatedOrdersForGuest;
use App\Notifications\CreateOrdersForLineGuest;
use App\Notifications\OrderCreatedLineNotify;
use App\Notifications\OrderCreatedNotifyToAdmin;
use App\Order;
use App\User;

class OrderObserver
{
    public function created(Order $order)
    {
        if (OrderType::NOMINATED_CALL == $order->type || OrderType::CALL == $order->type) {
            if (ProviderType::LINE != $order->user->provider) {
                $order->user->notify(
                    (new CreateNominatedOrdersForGuest($order->id))->delay(now()->addSeconds(3))
                );
            }
        }

        if (ProviderType::LINE == $order->user->provider) {
            $order->user->notify(
                (new CreateOrdersForLineGuest($order->id))->delay(now()->addSeconds(3))
            );
        }

        $admin = User::find(1);
        $delay = now()->addSeconds(3);
        $admin->notify(
            (new OrderCreatedNotifyToAdmin($order->id))->delay($delay)
        );
        $admin->notify(
            (new OrderCreatedLineNotify($order->id))->delay($delay)
        );
    }

    public function updated(Order $order)
    {
        if ($order->getOriginal('payment_status') != $order->payment_status) {
            if (OrderPaymentStatus::PAYMENT_FINISHED == $order->payment_status) {
                $order->user->notify(new CompletedPayment($order));
            }
        }
    }
}
