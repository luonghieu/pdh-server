<?php

namespace App\Notifications;

use App\Enums\DeviceType;
use App\Enums\MessageType;
use App\Enums\ProviderType;
use App\Enums\RoomType;
use App\Enums\SystemMessageType;
use App\Enums\UserType;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class GuestCancelOrderOfferFromCast extends Notification implements ShouldQueue
{
    use Queueable;

    public $offer;

    /**
     * Create a new notification instance.
     *
     * @param $offer
     */
    public function __construct($offer)
    {
        $this->offer = $offer;
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

            if (DeviceType::WEB == $notifiable->device_type && UserType::GUEST == $notifiable->type) {
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
        $castPrivateRoom = $notifiable->rooms()
            ->where('rooms.type', RoomType::SYSTEM)
            ->where('rooms.is_active', true)->first();
        $message = '予約リクエストがキャンセルされました';

        $castPrivateRoomMessage = $castPrivateRoom->messages()->create([
            'user_id' => 1,
            'type' => MessageType::SYSTEM,
            'message' => $message,
            'system_type' => SystemMessageType::NORMAL,
        ]);

        $castPrivateRoomMessage->recipients()->attach($notifiable->id, ['room_id' => $castPrivateRoom->id]);

        $pushId = 'c_25';
        $room = $castPrivateRoom;

        $namedUser = 'user_' . $notifiable->id;
        $send_from = UserType::ADMIN;

        return [
            'audienceOptions' => ['named_user' => $namedUser],
            'notificationOptions' => [
                'alert' => $message,
                'ios' => [
                    'alert' => $message,
                    'sound' => 'cat.caf',
                    'badge' => '+1',
                    'content-available' => true,
                    'extra' => [
                        'push_id' => $pushId,
                        'send_from' => $send_from,
                        'cast_offer_id' => $this->offer->id,
                        'room_id' => $room->id,
                    ],
                ],
                'android' => [
                    'alert' => $message,
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
        $castPrivateRoom = $notifiable->rooms()
            ->where('rooms.type', RoomType::SYSTEM)
            ->where('rooms.is_active', true)->first();
        $message = '予約リクエストがキャンセルされました';
        $castPrivateRoomMessage = $castPrivateRoom->messages()->create([
            'user_id' => 1,
            'type' => MessageType::SYSTEM,
            'message' => $message,
            'system_type' => SystemMessageType::NORMAL,
        ]);

        $castPrivateRoomMessage->recipients()->attach($notifiable->id, ['room_id' => $castPrivateRoom->id]);

        return [
            [
                'type' => 'text',
                'text' => $message,
            ],
        ];
    }
}
