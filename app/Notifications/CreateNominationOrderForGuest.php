<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class CreateNominationOrderForGuest extends Notification implements ShouldQueue
{
    use Queueable;

    public $orderId;

    /**
     * Create a new notification instance.
     *
     * @param $orderId
     */
    public function __construct($orderId)
    {
        $this->orderId = $orderId;
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
        $link = route('admin.orders.nominees', ['order' => $this->orderId]);
        return [
            'text' => "売上申請の修正依頼がありました。[Link]($link)"
        ];
    }
}
