<?php

namespace App\Notifications;

use App\Enums\MessageType;
use App\Enums\SystemMessageType;
use App\Enums\UserType;
use App\Traits\DirectRoom;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class CastDenyOrders extends Notification implements ShouldQueue
{
    use Queueable, DirectRoom;

    public $order;
    public $cast;

    /**
     * Create a new notification instance.
     *
     * @param $order
     * @param $cast
     */
    public function __construct($order, $cast)
    {
        $this->order = $order;
        $this->cast = $cast;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return [CustomDatabaseChannel::class, PushNotificationChannel::class];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     */
    public function toMail($notifiable)
    {
        return;
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        $room = $this->createDirectRoom($this->order->user_id, $this->cast->id);
        $roomMesage = '提案がキャンセルされました。';
        $roomMessage = $room->messages()->create([
            'user_id' => 1,
            'type' => MessageType::SYSTEM,
            'message' => $roomMesage,
            'system_type' => SystemMessageType::NOTIFY
        ]);
        $roomMessage->recipients()->attach($notifiable->id, ['room_id' => $room->id]);

        $notifyMessage = '残念ながらマッチングが成立しませんでした（；；）';

        return [
            'content' => $notifyMessage,
            'send_from' => UserType::ADMIN,
        ];
    }

    public function pushData($notifiable)
    {
        $room = $this->createDirectRoom($this->order->user_id, $this->cast->id);
        $content = '残念ながらマッチングが成立しませんでした（；；）';

        $namedUser = 'user_' . $notifiable->id;
        $send_from = UserType::ADMIN;
        $pushId = 'g_9';

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
                        'order_id' => $this->order->id,
                        'room_id' => $room->id
                    ],
                ],
                'android' => [
                    'alert' => $content,
                    'extra' => [
                        'push_id' => $pushId,
                        'send_from' => $send_from,
                        'order_id' => $this->order->id,
                        'room_id' => $room->id
                    ],
                ]
            ],
        ];
    }
}
