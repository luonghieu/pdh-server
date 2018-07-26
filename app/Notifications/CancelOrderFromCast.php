<?php

namespace App\Notifications;

use App\Enums\MessageType;
use App\Enums\RoomType;
use App\Enums\UserType;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class CancelOrderFromCast extends Notification implements ShouldQueue
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
        if ($notifiable->type == UserType::CAST) {
            $room = $notifiable->rooms()
                ->where('rooms.type', RoomType::SYSTEM)
                ->where('rooms.is_active', true)->first();
            $message = 'キャンセルが完了しました。';

            $roomMessage = $room->messages()->create([
                'user_id' => 1,
                'type' => MessageType::SYSTEM,
                'message' => $message
            ]);

            $roomMessage->recipients()->attach($notifiable->id, ['room_id' => $room->id]);
        } else {
            $room = $this->order->room;
            $message = '予約がキャンセルされました。';
            $roomMessage = $room->messages()->create([
                'user_id' => 1,
                'type' => MessageType::SYSTEM,
                'message' => $message
            ]);

            $roomMessage->recipients()->attach($notifiable->id, ['room_id' => $room->id]);
        }

        return [
            'content' => $message,
            'send_from' => UserType::ADMIN,
        ];
    }

    public function pushData($notifiable)
    {
        if ($notifiable->type == UserType::CAST) {
            $content = 'キャンセルが完了しました。';
            $pushId = 'c_10';
        } else {
            $pushId = 'g_10';
            $content = '予約がキャンセルされました。';
        }

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
