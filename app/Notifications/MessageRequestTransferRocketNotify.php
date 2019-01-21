<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;

class MessageRequestTransferRocketNotify extends Notification implements ShouldQueue
{
    use Queueable;

    public $userId;
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($userId)
    {
        $this->userId = $userId;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return [RocketChatNotificationChannel::class];
    }

    public function rocketChatPushData($notifiable)
    {
        $link = route('admin.request_transfer.show', ['user' => $this->userId]);
        $content = '新規のキャスト申請がありました。'
            . PHP_EOL . 'Link: ' . $link;

        return [
            [
                'type' => 'text',
                'text' => $content,
            ]
        ];
    }
}
