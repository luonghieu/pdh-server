<?php

namespace App\Notifications;

use App\Enums\OrderType;
use App\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;

class PaymentRequestUpdateLineNotify extends Notification implements ShouldQueue
{
    use Queueable;

    public $order;

    /**
     * Create a new notification instance.
     *
     * @param Order $order
     */
    public function __construct(Order $order)
    {
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
        return [LineBotGroupNotificationChannel::class];
    }

    public function lineBotPushToGroupData($notifiable)
    {
        if (OrderType::CALL == $this->order->type) {
            $link = route('admin.orders.call', ['order' => $this->order->id]);
        } else {
            $link = route('admin.orders.order_nominee', ['room' => $this->order->id]);
        }

        $content = '売上申請の修正依頼がありました。';

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
