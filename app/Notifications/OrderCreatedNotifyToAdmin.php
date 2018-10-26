<?php

namespace App\Notifications;

use App\Enums\OrderType;
use App\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;

class OrderCreatedNotifyToAdmin extends Notification implements ShouldQueue
{
    use Queueable;

    public $order;

    /**
     * Create a new notification instance.
     *
     * @param $orderId
     */
    public function __construct($orderId)
    {
        $order = Order::findOrFail($orderId);

        $this->order = $order;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return [RocketChatNotificationChannel::class];
    }

    public function rocketChatPushData($notifiable)
    {
        if ($this->order->type == OrderType::NOMINATION) {
            $link = route('admin.orders.nominees', ['order' => $this->order->id]);
        } else {
            $link = route('admin.orders.call', ['order' => $this->order->id]);
        }

        return [
            'text' => "売上申請の修正依頼がありました。[Link]($link)"
        ];
    }
}
