<?php

namespace App\Notifications;

use App\Enums\ProviderType;
use App\Enums\UserType;
use App\Room;
use App\Services\Line;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Carbon;

class ApproveNominatedOrders extends Notification implements ShouldQueue
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
     * @param  mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        if ($notifiable->provider == ProviderType::LINE) {
            return [LineBotNotificationChannel::class];
        } else {
            return [PushNotificationChannel::class];
        }
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed $notifiable
     */
    public function toMail($notifiable)
    {
        return;
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [];
    }

    public function pushData($notifiable)
    {
        $room = Room::find($this->order->room_id);

        $startTime = Carbon::parse($this->order->date . ' ' . $this->order->start_time);

        $content = '\\\\ おめでとうございます！マッチングが確定しました♪ //'
            . PHP_EOL . PHP_EOL . '- ご予約内容 - '
            . PHP_EOL . '場所：' . $this->order->address
            . PHP_EOL . '合流予定時間：' . $startTime->format('H:i') . '～'
            . PHP_EOL . PHP_EOL . 'ゲストの方はキャストに来て欲しい場所の詳細をお伝えください。'
            . PHP_EOL . '尚、ご不明点がある場合は運営までお問い合わせください。'
            . PHP_EOL . PHP_EOL . 'それでは素敵な時間をお楽しみください♪';

        $namedUser = 'user_' . $notifiable->id;
        $send_from = UserType::ADMIN;

        if ($notifiable->type == UserType::GUEST) {
            $pushId = 'g_3';
        } else {
            $pushId = 'c_3';
        }

        return [
            'audienceOptions' => ['named_user' => $namedUser],
            'notificationOptions' => [
                'alert' => $content,
                'ios' => [
                    'alert' => $content,
                    'sound' => 'cat.caf',
                    'badge' => '+1',
                    'content-available' => true,
                    'extra' => [
                        'push_id' => $pushId,
                        'send_from' => $send_from,
                        'order_id' => $this->order->id,
                        'room_id' => ($room) ? $room->id : ''
                    ],
                ],
                'android' => [
                    'alert' => $content,
                    'extra' => [
                        'push_id' => $pushId,
                        'send_from' => $send_from,
                        'order_id' => $this->order->id,
                        'room_id' => ($room) ? $room->id : ''
                    ],
                ]
            ]
        ];
    }

    public function lineBotPushData($notifiable)
    {
        $room = Room::find($this->order->room_id);
        $startTime = Carbon::parse($this->order->date . ' ' . $this->order->start_time);

        $firstMessage = '\\\\ おめでとうございます！マッチングが確定しました🎊//';
        $secondMessage = '▼ご予約内容'
            . PHP_EOL . '場所：' . $this->order->address
            . PHP_EOL . '合流予定時間：' . $startTime->format('H:i') . '～'
            . PHP_EOL . PHP_EOL .'ゲストの方はキャストに来て欲しい場所の詳細をお伝えください。';

        $line = new Line();
        $liffId = $line->getLiffId(route('message.messages', ['room' => $room->id]));

        return [
            [
                'type' => 'text',
                'text' => $firstMessage
            ],
            [
                'type' => 'template',
                'altText' => $secondMessage,
                'text' => $secondMessage,
                'template' => [
                    'type' => 'buttons',
                    'text' => $secondMessage,
                    'actions' => [
                        [
                            'type' => 'uri',
                            'label' => 'メッセージを確認する',
                            'uri' => "line://app/$liffId"
                        ]
                    ]
                ]
            ]
        ];
    }
}
