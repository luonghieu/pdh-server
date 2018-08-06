<?php

namespace App\Notifications;

use App\Enums\NotificationStyle;
use App\Enums\RoomType;
use App\Enums\UserType;
use App\Enums\MessageType;
use Illuminate\Bus\Queueable;
use App\Enums\SystemMessageType;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class TenMinBeforeOrderEnded extends Notification implements ShouldQueue
{
    use Queueable;

    public $order;
    public $cast;

    /**
     * Create a new notification instance.
     *
     * @param $order
     * @param null $cast
     */
    public function __construct($order, $cast = null)
    {
        $this->order = $order;
        $this->cast = $cast;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return [CustomDatabaseChannel::class, PushNotificationChannel::class];
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
        $message = $this->cast->nickname . 'の解散予定時刻まで残り10分です。';
        $room = $this->order->room;

        $roomMessage = $room->messages()->create([
            'user_id' => 1,
            'type' => MessageType::SYSTEM,
            'system_type' => SystemMessageType::NOTIFY,
            'message' => $message
        ]);

        $roomMessage->recipients()->attach($notifiable->id, ['room_id' => $room->id, 'is_show' => false]);

        return [
            'content' => $message,
            'send_from' => UserType::ADMIN,
        ];
    }

    public function pushData($notifiable)
    {
        $content = $this->cast->nickname . 'の解散予定時刻まで残り10分です。';
        $pushId = 'g_5';

        $namedUser = 'user_' . $notifiable->id;
        $send_from = UserType::ADMIN;


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
                        'order_id' => $this->order->id
                    ],
                ],
            ],
        ];
    }
}
