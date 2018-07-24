<?php

namespace App\Notifications;

use App\Enums\MessageType;
use App\Enums\UserType;
use App\Guest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class CastAcceptedOrderNomination extends Notification implements ShouldQueue
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
        $order = $this->order;
        $room = $order->room;

        if ($notifiable->type == UserType::CAST) {
            $castMessage = 'マッチングが確定しました。';

            $roomMessage = $room->messages()->create([
                'user_id' => 1,
                'type' => MessageType::SYSTEM,
                'message' => $castMessage
            ]);

            $roomMessage->recipients()->attach($notifiable->id, ['room_id' => $room->id, 'is_show' => false]);
        }

        $message = 'マッチング確定おめでとうございます♪'
            . '\n 合流後はタイマーで時間計測を行い、解散予定の10分前には通知が届きます。'
            . '\n ※解散予定時刻後は自動で延長されます。'
            . '\n \n その他ご不明点がある場合は運営までお問い合わせください。'
            . '\n \n それでは素敵な時間をお楽しみください♪';

        $roomMessage = $room->messages()->create([
            'user_id' => 1,
            'type' => MessageType::SYSTEM,
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
        $content = 'マッチング確定おめでとうございます♪'
            . '\n 合流後はタイマーで時間計測を行い、解散予定の10分前には通知が届きます。'
            . '\n ※解散予定時刻後は自動で延長されます。'
            . '\n \n その他ご不明点がある場合は運営までお問い合わせください。'
            . '\n \n それでは素敵な時間をお楽しみください♪';

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
                        'order_id' => $this->order->id
                    ],
                ],
            ],
        ];
    }
}
