<?php

namespace App\Notifications;

use App\Enums\ProviderType;
use App\Enums\UserType;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class AdminNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $schedule;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($schedule)
    {
        $this->schedule = $schedule;
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
            return [CustomDatabaseChannel::class, LineBotNotificationChannel::class];
        } else {
            return [CustomDatabaseChannel::class, PushNotificationChannel::class];
        }
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed $notifiable
     * @return void
     */
    public function toMail($notifiable)
    {
        //
    }

    public function toArray($notifiable)
    {
        $content = $this->schedule->content;
        $send_from = UserType::ADMIN;

        return [
            'content' => $content,
            'send_from' => $send_from,
        ];
    }

    public function pushData($notifiable)
    {
        $content = $this->schedule->content;
        $content = removeHtmlTags($content);

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
                        'send_from' => $send_from,
                    ],
                ],
                'android' => [
                    'alert' => $content,
                    'extra' => [
                        'send_from' => $send_from,
                    ],
                ],
            ],
        ];
    }

    public function lineBotPushData($notifiable)
    {
        $content = $this->schedule->content;
        $content = removeHtmlTags($content);

        return [
            [
                'type' => 'text',
                'text' => $content,
            ],
        ];
    }
}
