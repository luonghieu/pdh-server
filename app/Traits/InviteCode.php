<?php

namespace App\Traits;

use App\Enums\InviteCodeHistoryStatus;
use App\Enums\OrderPaymentStatus;
use App\Enums\OrderStatus;
use App\Order;

trait InviteCode
{
    public function updateInvateCodeHistory($orderId)
    {
        $order = Order::find($orderId);
        $user = $order->user;
        $inviteCodeHistory = $user->inviteCodeHistory;
        if ($inviteCodeHistory) {
            if ($inviteCodeHistory->status == InviteCodeHistoryStatus::PENDING) {
                $nextOrder = $user->orders()->where('id', '>', $order->id)
                    ->whereIn('status', [OrderStatus::OPEN, OrderStatus::ACTIVE, OrderStatus::DONE])
                    ->where(function($q) {
                        $q->where('payment_status', null)
                            ->orWhereIn('payment_status', [OrderPaymentStatus::WAITING, OrderPaymentStatus::REQUESTING, OrderPaymentStatus::EDIT_REQUESTING]);
                    })->first();

                if ($nextOrder) {
                    $inviteCodeHistory->order_id = $nextOrder->id;
                } else {
                    $inviteCodeHistory->order_id = null;
                }

                $inviteCodeHistory->save();
            }
        }
    }
}
