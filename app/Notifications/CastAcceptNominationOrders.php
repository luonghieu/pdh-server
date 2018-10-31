<?php

namespace App\Notifications;

use App\Enums\ProviderType;
use App\Enums\UserType;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class CastAcceptNominationOrders extends Notification implements ShouldQueue
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
        if ($notifiable->provider == ProviderType::LINE) {
            return [CustomDatabaseChannel::class, LineBotNotificationChannel::class];
        } else {
            return [CustomDatabaseChannel::class, PushNotificationChannel::class];
        }
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     */
    public function toMail($notifiable)
    {
        return;
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        if ($notifiable->type == UserType::GUEST) {
            $message = 'おめでとうございます！'
                . PHP_EOL . 'キャストとのマッチングが確定しました♪';
        } else {
            $message = 'おめでとう！ゲストとのマッチングが確定しました♪';
        }

        return [
            'content' => $message,
            'send_from' => UserType::ADMIN,
        ];
    }

    public function pushData($notifiable)
    {
        if ($notifiable->type == UserType::GUEST) {
            $content = 'おめでとうございます！'
                . PHP_EOL . 'キャストとのマッチングが確定しました♪';
        } else {
            $content = 'おめでとう！ゲストとのマッチングが確定しました♪';
        }

        $namedUser = 'user_' . $notifiable->id;
        $send_from = UserType::ADMIN;
        $pushId = ($notifiable->type == UserType::GUEST) ? 'g_8' : 'c_8';

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
                        'room_id' => $this->order->room->id
                    ],
                ],
                'android' => [
                    'alert' => $content,
                    'extra' => [
                        'push_id' => $pushId,
                        'send_from' => $send_from,
                        'order_id' => $this->order->id,
                        'room_id' => $this->order->room->id
                    ],
                ]
            ],
        ];
    }

    public function lineBotPushData($notifiable)
    {
        $startTime = Carbon::parse($this->order->date . ' ' . $this->order->start_time);
        $firstMessage = '\\\\ おめでとうございます！マッチングが確定しました🎊//';
        $secondMessage = '▼ご予約内容'
            . PHP_EOL . '場所：' . $this->order->address
            . PHP_EOL . '合流予定時間：' . $startTime->format('Y/m/d H:i') . '～'
            . PHP_EOL . PHP_EOL .'ゲストの方はキャストに来て欲しい場所の詳細をお伝えください。';

        $page = env('LINE_LIFF_REDIRECT_PAGE') . '?page=room&room_id=' . $this->order->room->id . '&order_id=' .
            $this->order->id;

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
                            'uri' => "line://app/$page"
                        ]
                    ]
                ]
            ]
        ];
    }
}
