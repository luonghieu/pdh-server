<?php

namespace App\Notifications;

use App\Enums\OrderType;
use App\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;

class OrderCreatedLineNotify extends Notification implements ShouldQueue
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
        $order = Order::onWriteConnection()->findOrFail($orderId);

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
        if (env('LINE_GROUP_ID')) {
            return [LineBotGroupNotificationChannel::class];
        }

        return [];
    }

    public function lineBotPushToGroupData($notifiable)
    {
        if ($this->order->type == OrderType::NOMINATION) {
            $link = route('admin.orders.order_nominee', ['order' => $this->order->id]);
        } else {
            $link = route('admin.orders.call', ['order' => $this->order->id]);
        }

        $content = '新規の予約がありました。';

        return [
            [
                'type' => 'template',
                'altText' => $content,
                'text' => $content,
                'template' => [
                    'type' => 'buttons',
                    'text' => $content,
                    'actions' => [
                        [
                            'type' => 'uri',
                            'label' => '確認する',
                            'uri' => $link
                        ]
                    ]
                ]
            ]
        ];
    }
}
