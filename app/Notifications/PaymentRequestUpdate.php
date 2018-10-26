<?php

namespace App\Notifications;

use App\Enums\RoomType;
use App\Enums\UserType;
use App\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PaymentRequestUpdate extends Notification
{
    use Queueable;

    protected $order;
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Order $order)
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
        return [CustomDatabaseChannel::class, RocketChatNotificationChannel::class];
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        $message = '予約ID ' . $this->order->id . ' のステータスが、「売上申請修正依頼中」になりました。対応してください。';

        return [
            'content' => $message,
            'send_from' => UserType::ADMIN,
        ];
    }

    public function rocketChatPushData($notifiable)
    {
        $systemRoom = $this->order->user->rooms()->where('type', RoomType::SYSTEM)->first();
        $link = route('admin.chat.index', ['room' => $systemRoom->id]);
        return [
            'text' => "運営者チャットにメッセージが届きました。[Link]($link)"
        ];
    }
}
