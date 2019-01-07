<?php

namespace App\Notifications;

use App\Enums\DeviceType;
use App\Enums\ProviderType;
use App\Enums\UserType;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;

class FavoritedNotify extends Notification implements ShouldQueue
{
    use Queueable;

    public $user;

    /**
     * Create a new notification instance.
     *
     * @param $user
     */
    public function __construct($user)
    {
        $this->user = $user;
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
        $content = $this->user->nickname . 'さんからイイネされました！';
        $namedUser = 'user_' . $notifiable->id;
        $send_from = UserType::ADMIN;
        if ($notifiable->type == UserType::CAST) {
            $pushId = 'c_19';
        } else {
            $pushId = 'g_17';
        }

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
        $content = $this->user->nickname . 'さんからイイネされました！';
        $page = env('LINE_LIFF_REDIRECT_PAGE') . '?page=cast&cast_id=' . $this->user->id;

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
                            'label' => 'キャストを見てみる',
                            'uri' => "line://app/$page"
                        ]
                    ]
                ]
            ]
        ];
    }
}
