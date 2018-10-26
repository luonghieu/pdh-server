<?php

namespace App\Notifications;

use App\Enums\UserType;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class CallOrdersCreated extends Notification implements ShouldQueue
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
        $content = '新着のキャスト募集が追加されました♪';

        return [
            'content' => $content,
            'send_from' => UserType::ADMIN,
        ];
    }

    public function pushData($notifiable)
    {
        $content = '新着のキャスト募集が追加されました♪';

        $namedUser = 'user_' . $notifiable->id;
        $send_from = UserType::ADMIN;
        $pushId = 'c_18';

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
                    ],
                ],
                'android' => [
                    'alert' => $content,
                    'extra' => [
                        'push_id' => $pushId,
                        'send_from' => $send_from,
                        'order_id' => $this->order->id,
                    ],
                ]
            ],
        ];
    }
}
