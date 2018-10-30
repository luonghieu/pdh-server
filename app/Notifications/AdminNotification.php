<?php

namespace App\Notifications;

use App\Enums\DeviceType;
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
        if ($notifiable->provider == ProviderType::LINE) {
            if ($notifiable->type == UserType::GUEST && $notifiable->device_type == null) {
                return [CustomDatabaseChannel::class, LineBotNotificationChannel::class];
            }

            if ($notifiable->type == UserType::CAST && $notifiable->device_type == null) {
                return [CustomDatabaseChannel::class, PushNotificationChannel::class];
            }

            if ($this->schedule->device_type == DeviceType::ALL) {
                return [CustomDatabaseChannel::class, LineBotNotificationChannel::class, PushNotificationChannel::class];
            }

            if ($this->schedule->device_type == DeviceType::WEB) {
                return [CustomDatabaseChannel::class, LineBotNotificationChannel::class];
            }

            return [CustomDatabaseChannel::class, PushNotificationChannel::class];
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
        $schedule = $this->schedule;
        $content = $schedule->content;
        $send_from = UserType::ADMIN;

        return [
            'title' => $schedule->title,
            'content' => $content,
            'send_from' => $send_from,
        ];
    }

    public function pushData($notifiable)
    {
        $schedule = $this->schedule;
        $content = $schedule->content;
        $content = removeHtmlTags($content);

        $namedUser = 'user_' . $notifiable->id;
        $send_from = UserType::ADMIN;

        $scheduleDeviceType = $schedule->device_type;
        if ($scheduleDeviceType == DeviceType::IOS) {
            $devices = ['ios'];
        } elseif ($scheduleDeviceType == DeviceType::ANDROID) {
            $devices = ['android'];
        } else {
            $devices = ['android', 'ios'];
        }

        return [
            'device' => $devices,
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
