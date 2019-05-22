<?php

namespace App\Notifications;

use App\CastOffer;
use App\Enums\DeviceType;
use App\Enums\MessageType;
use App\Enums\ProviderType;
use App\Enums\SystemMessageType;
use App\Enums\UserType;
use App\Order;
use App\Traits\DirectRoom;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class CastCreateOffer extends Notification implements ShouldQueue
{
    use Queueable, DirectRoom;

    public $order;

    /**
     * Create a new notification instance.
     *
     * @param $offerId
     */
    public function __construct($offerId)
    {
        $this->order = Order::onWriteConnection()->find($offerId);
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        if (ProviderType::LINE == $notifiable->provider) {
            if (UserType::GUEST == $notifiable->type && null == $notifiable->device_type) {
                return [LineBotNotificationChannel::class];
            }

            if (UserType::CAST == $notifiable->type && null == $notifiable->device_type) {
                return [PushNotificationChannel::class];
            }

            if (DeviceType::WEB == $notifiable->device_type) {
                return [LineBotNotificationChannel::class];
            } else {
                return [PushNotificationChannel::class];
            }
        } else {
            return [PushNotificationChannel::class];
        }
    }

    public function pushData($notifiable)
    {
        $cast = $this->order->nominees()->first();
        $room = $this->createDirectRoom($this->order->user_id, $cast->id);
        $roomMesage = 'マッチング確定おめでとうございます♪'
            . PHP_EOL . '合流後はタイマーで時間計測を行い、解散予定の10分前には通知が届きます。'
            . PHP_EOL . '※解散予定時刻後は自動で延長されます。'
            . PHP_EOL . PHP_EOL . 'その他ご不明点がある場合は運営までお問い合わせください。'
            . PHP_EOL . PHP_EOL . 'それでは素敵な時間をお楽しみください♪';
        $roomMessage = $room->messages()->create([
            'user_id' => 1,
            'type' => MessageType::SYSTEM,
            'message' => $roomMesage,
            'system_type' => SystemMessageType::NORMAL,
        ]);
        $roomMessage->recipients()->attach($notifiable->id, ['room_id' => $room->id]);

        $namedUser = 'user_' . $notifiable->id;
        $send_from = UserType::ADMIN;
        $pushId = 'g_24';

        return [
            'audienceOptions' => ['named_user' => $namedUser],
            'notificationOptions' => [
                'alert' => $roomMesage,
                'ios' => [
                    'alert' => $roomMesage,
                    'sound' => 'cat.caf',
                    'badge' => '+1',
                    'content-available' => true,
                    'extra' => [
                        'push_id' => $pushId,
                        'send_from' => $send_from,
                        'order_id' => $this->offer->id,
                        'room_id' => $room->id,
                    ],
                ],
                'android' => [
                    'alert' => $roomMesage,
                    'extra' => [
                        'push_id' => $pushId,
                        'send_from' => $send_from,
                        'cast_offer_id' => $this->offer->id,
                        'room_id' => $room->id,
                    ],
                ],
            ],
        ];
    }

    public function lineBotPushData($notifiable)
    {
        $cast = $this->order->nominees()->first();
        $room = $this->createDirectRoom($this->order->user_id, $cast->id);
        $roomMesage = 'マッチング確定おめでとうございます♪'
            . PHP_EOL . '合流後はタイマーで時間計測を行い、解散予定の10分前には通知が届きます。'
            . PHP_EOL . '※解散予定時刻後は自動で延長されます。'
            . PHP_EOL . PHP_EOL . 'その他ご不明点がある場合は運営までお問い合わせください。'
            . PHP_EOL . PHP_EOL . 'それでは素敵な時間をお楽しみください♪';
        $roomMessage = $room->messages()->create([
            'user_id' => 1,
            'type' => MessageType::SYSTEM,
            'message' => $roomMesage,
            'system_type' => SystemMessageType::NORMAL,
        ]);
        $roomMessage->recipients()->attach($notifiable->id, ['room_id' => $room->id]);

        $content = $cast->nickname . 'さんから予約リクエストがありました'
            . PHP_EOL . '下記のボタンをタップして、予約リクエストを確認してください。';

        $page = env('LINE_LIFF_REDIRECT_PAGE') . '?page=cast_offer&cast_offer_id=' . $this->order->id;

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
                            'label' => '予約リクエストを確認する',
                            'uri' => "line://app/$page",
                        ],
                    ],
                ],
            ],
        ];
    }
}
