<?php

namespace App\Observers;

use App\Enums\OrderPaymentStatus;
use App\Enums\OrderType;
use App\Enums\ProviderType;
use App\Notifications\CompletedPayment;
use App\Notifications\CreateNominatedOrdersForGuest;
use App\Notifications\CreateNominationOrderForGuest;
use App\Notifications\CreateOrdersForLineGuest;
use App\Notifications\OrderCreatedNotifyToAdmin;
use App\Order;
use App\User;

class OrderObserver
{
    public function created(Order $order)
    {
        if (OrderType::NOMINATED_CALL == $order->type || OrderType::CALL == $order->type) {
            if ($order->user->provider != ProviderType::LINE) {
                $order->user->notify(new CreateNominatedOrdersForGuest($order->id));
            }
        }

        if ($order->user->provider == ProviderType::LINE) {
            $order->user->notify(new CreateOrdersForLineGuest($order->id));
        }

        $admin = User::find(1);
        $admin->notify(new OrderCreatedNotifyToAdmin($order->id));
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
