<?php

namespace App\Notifications;

use App\Enums\OrderType;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;

class AutoChargeFailedLineNotify extends Notification implements ShouldQueue
{
    use Queueable;

    public $order;

    /**
     * Create a new notification instance.
     *
     * @param $order
     */
    public function __construct($order)
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
        if (env('LINE_GROUP_ID')) {
            return [LineBotGroupNotificationChannel::class];
        }

        return [];
    }

    public function lineBotPushToGroupData($notifiable)
    {

        if (OrderType::NOMINATION != $this->order->type) {
            $link = route('admin.orders.call', ['order' => $this->order->id]);
        } else {
            $link = route('admin.orders.order_nominee', ['room' => $this->order->id]);
        }
        $content = '決済エラーが発生しました';

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
