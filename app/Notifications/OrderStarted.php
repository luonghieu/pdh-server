<?php

namespace App\Notifications;

use App\Enums\MessageType;
use App\Enums\UserType;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class OrderStarted extends Notification
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
        $room = $this->order->room;

        if ($notifiable->type == UserType::GUEST) {
            $roomMessage = $room->messages()->create([
                'user_id' => 1,
                'type' => MessageType::SYSTEM,
                'message' => '「' . $this->cast->nickname . 'さんが合流しました。」'
            ]);

            $roomMessage->recipients()->attach($notifiable->id, ['room_id' => $room->id]);
        }

    }

    public function pushData($notifiable)
    {
        if ($notifiable->type == UserType::GUEST) {
            $content = '「' . $this->cast->nickname . 'さんが合流しました。」';
            $pushId = 'g_4';
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
                    ],
                ],
            ],
        ];
    }
}
