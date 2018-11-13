<?php

namespace App\Notifications;

use App\Verification;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class ResendVerificationCode extends Notification
{
    use Queueable;

    public $verification;

    /**
     * Create a new notification instance.
     *
     * @return void
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
        return [RocketChatNotificationChannel::class];
    }

    public function rocketChatPushData($notifiable)
    {
        return [
            'text' => "新着のSMS認証依頼が届きました
                電話番号:【{$this->verification->phone}】
                認証番号:【{$this->verification->code}】"
        ];
    }
}