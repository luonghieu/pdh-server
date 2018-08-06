<?php

namespace App\Notifications;

use App\Enums\MessageType;
use App\Enums\NotificationStyle;
use App\Enums\RoomType;
use App\Enums\SystemMessageType;
use App\Enums\UserType;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class CreateNominatedOrdersForGuest extends Notification implements ShouldQueue
{
    use Queueable;

    public $order;

    /**
     * Create a new notification instance.
     *
     * @param $order
     */
    public function __construct($order)
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
        $startTime = Carbon::parse($this->order->date . ' ' . $this->order->start_time);
        $endTime = Carbon::parse($this->order->date . ' ' . $this->order->end_time);

        $message = 'Cheersをご利用いただきありがとうございます！'
        . PHP_EOL . 'キャストのご予約を承りました。'
        . PHP_EOL . '------------------------------------------'
        . PHP_EOL . PHP_EOL . '- ご予約内容 -'
        . PHP_EOL . '日時：' . $startTime->format('Y/m/d H:i') . '~'
        . PHP_EOL . '時間：' . $startTime->diffInMinutes($endTime) / 60 . '時間'
        . PHP_EOL . 'クラス：' . $this->order->castClass->name
        . PHP_EOL . '人数：' . $this->order->total_cast . '人'
        . PHP_EOL . '場所：' . $this->order->address
            . PHP_EOL . PHP_EOL . '現在、キャストの調整を行っております。'
            . PHP_EOL . 'しばらくお待ちください☆';

        $room = $notifiable->rooms()
            ->where('rooms.type', RoomType::SYSTEM)
            ->where('rooms.is_active', true)->first();

        $roomMessage = $room->messages()->create([
            'user_id' => 1,
            'type' => MessageType::SYSTEM,
            'message' => $message,
            'system_type' => SystemMessageType::NOTIFY,
        ]);

        $roomMessage->recipients()->attach($notifiable->id, ['room_id' => $room->id]);

        return [
            'content' => $message,
            'send_from' => UserType::ADMIN,
        ];
    }

    public function pushData($notifiable)
    {
        $startTime = Carbon::parse($this->order->date . ' ' . $this->order->start_time);
        $endTime = Carbon::parse($this->order->date . ' ' . $this->order->end_time);
        $content = 'Cheersをご利用いただきありがとうございます！'
        . PHP_EOL . 'キャストのご予約を承りました。'
        . PHP_EOL . '------------------------------------------'
        . PHP_EOL . PHP_EOL . '- ご予約内容 -'
        . PHP_EOL . '日時：' . $startTime->format('Y/m/d H:i') . '~'
        . PHP_EOL . '時間：' . $startTime->diffInMinutes($endTime) / 60 . '時間'
        . PHP_EOL . 'クラス：' . $this->order->castClass->name
        . PHP_EOL . '人数：' . $this->order->total_cast . '人'
        . PHP_EOL . '場所：' . $this->order->address
            . PHP_EOL . PHP_EOL . '現在、キャストの調整を行っております。'
            . PHP_EOL . 'しばらくお待ちください☆';

        $namedUser = 'user_' . $notifiable->id;
        $send_from = UserType::ADMIN;
        $pushId = 'g_2';

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
