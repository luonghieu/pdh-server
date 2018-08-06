<?php

namespace App\Notifications;

use Carbon\Carbon;
use App\Enums\UserType;
use App\Enums\MessageType;
use Illuminate\Bus\Queueable;
use App\Enums\SystemMessageType;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class OrderCompleted extends Notification
{
    use Queueable;

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
        $order = $this->order;
        $room = $order->room;
        $cast = $order->casts()
            ->withPivot('started_at', 'stopped_at', 'type')
            ->where('user_id', $this->cast->id)
            ->first();

        $message = Carbon::parse($cast->pivot->stopped_at)->format('H:i')
            . PHP_EOL . $this->cast->nickname . 'が解散しました。';
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
        $cast = $this->order->casts()
            ->withPivot('started_at', 'stopped_at', 'type')
            ->where('user_id', $this->cast->id)
            ->first();

        $content = Carbon::parse($cast->pivot->stopped_at)->format('H:i')
            . PHP_EOL . $this->cast->nickname . 'が解散しました。';
        $pushId = 'c_7';
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
                    ],
                ],
            ],
        ];
    }
}
