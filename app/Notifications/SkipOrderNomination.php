<?php

namespace App\Notifications;

use App\Enums\MessageType;
use App\Enums\RoomType;
use App\Enums\UserType;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Enums\DeviceType;
use App\Enums\ProviderType;
use App\Enums\SystemMessageType;

class SkipOrderNomination extends Notification implements ShouldQueue
{
    use Queueable;
    public $nominee;
    /**
     * Create a new notification instance.
     *
     * @param $orderId
     */
    public function __construct($nominee){
        $this->nominee = $nominee;
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
            if ($notifiable->type == UserType::GUEST && $notifiable->device_type == null) {
                return [LineBotNotificationChannel::class];
            }

            if ($notifiable->type == UserType::CAST && $notifiable->device_type == null) {
                return [PushNotificationChannel::class];
            }

            if ($notifiable->device_type == DeviceType::WEB) {
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
        $content = '指名予約の提案が取り下げられました';

        $room = $notifiable->rooms()
            ->where('rooms.type', RoomType::DIRECT)
            ->where('rooms.is_active', true)->first();

        $roomMessage = $room->messages()->create([
            'user_id' => 1,
            'type' => MessageType::SYSTEM,
            'message' => $content,
            'system_type' => SystemMessageType::NOTIFY
        ]);

        $roomMessage->recipients()->attach($notifiable->id, ['room_id' => $room->id]);
        $roomMessage->recipients()->attach($this->nominee->id, ['room_id' => $room->id]);

        $namedUser = 'user_' . $notifiable->id;
        $send_from = UserType::ADMIN;
        $pushId = 'g_24';

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
                    ],
                ],
                'android' => [
                    'alert' => $content,
                    'extra' => [
                        'push_id' => $pushId,
                        'send_from' => $send_from,
                    ],
                ]
            ],
        ];
    }

    public function lineBotPushData($notifiable)
    {
        $content = '指名予約の提案が取り下げられました';

        $room = $notifiable->rooms()
            ->where('rooms.type', RoomType::DIRECT)
            ->where('rooms.is_active', true)->first();

        $roomMessage = $room->messages()->create([
            'user_id' => 1,
            'type' => MessageType::SYSTEM,
            'message' => $content,
            'system_type' => SystemMessageType::NOTIFY
        ]);

        $roomMessage->recipients()->attach($notifiable->id, ['room_id' => $room->id]);
        $roomMessage->recipients()->attach($this->nominee->id, ['room_id' => $room->id]);

        return [
            [
                'type' => 'text',
                'text' => $content,
            ]
        ];
    }
}
