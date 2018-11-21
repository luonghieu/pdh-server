<?php

namespace App\Notifications;

use App\Verification;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class ResendVerificationCodeLineNotify extends Notification implements ShouldQueue
{
    use Queueable;

    public $verification;

    /**
     * Create a new notification instance.
     *
     * @param $verificationId
     */
    public function __construct($verificationId)
    {
        $this->verification = Verification::onWriteConnection()->findOrFail($verificationId);
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return [LineBotGroupNotificationChannel::class];
    }

    public function lineBotPushToGroupData($notifiable)
    {
        $link = route('admin.verifications.index', ['search' => $this->verification->phone]);

        $content = '新着のSMS認証依頼が届きました。';

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
                            'label' => '確認する',
                            'uri' => $link
                        ]
                    ]
                ]
            ]
        ];
    }
}
