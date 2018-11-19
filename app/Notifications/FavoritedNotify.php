<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

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
        return [LineBotNotificationChannel::class];
    }

    public function lineBotPushData($notifiable)
    {
        $content = $this->user->nickname . 'さんからイイネされました！';
        $page = env('LINE_LIFF_REDIRECT_PAGE') . '?page=cast&id=' . $this->user->id;

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
