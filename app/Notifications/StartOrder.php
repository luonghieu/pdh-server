<?php

namespace App\Notifications;

use App\Enums\UserType;
use App\Enums\MessageType;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use App\Enums\SystemMessageType;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class StartOrder extends Notification implements ShouldQueue
{
    use Queueable;

    public $order;
    public $cast;

    /**
     * Create a new notification instance.
     *
     * @param $order
     * @param null $cast
     */
    public function __construct($order, $cast)
    {
        $this->order = $order;
        $this->cast = $cast;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return [CustomDatabaseChannel::class, PushNotificationChannel::class];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed $notifiable
     */
    public function toMail($notifiable)
    {
        return;
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        $room = $this->order->room;
        $message =  Carbon::now()->format('H:i')
            . PHP_EOL . $this->cast->nickname . 'さんが合流しました。';
        $roomMessage = $room->messages()->create([
            'user_id' => 1,
            'type' => MessageType::SYSTEM,
            'system_type' => SystemMessageType::NOTIFY,
            'message' => $message
        ]);

        $roomMessage->recipients()->attach($notifiable->id, ['room_id' => $room->id]);

        return [
            'content' => $message,
            'send_from' => UserType::ADMIN,
        ];
    }

    public function pushData($notifiable)
    {
        $content = Carbon::now()->format('H:i')
            . PHP_EOL . $this->cast->nickname . 'さんが合流しました。';
        $pushId = 'g_4';
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
                        'push_id' => $pushId,
                        'send_from' => $send_from,
                        'order_id' => $this->order->id
                    ],
                ],
            ],
        ];
    }
}
