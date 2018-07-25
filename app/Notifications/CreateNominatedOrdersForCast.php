<?php

namespace App\Notifications;

use App\Enums\MessageType;
use App\Enums\RoomType;
use App\Enums\UserType;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Carbon;

class CreateNominatedOrdersForCast extends Notification implements ShouldQueue
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
        $startTime = Carbon::parse($this->order->date . ' ' . $this->order->start_time);
        $endTime = Carbon::parse($this->order->date . ' ' . $this->order->end_time);

        $message = 'おめでとう！指名予約が入りました♪'
            . '\n ------------------------------------------'
            . '\n \n - ご予約内容 -'
            . '\n 日時：' . $startTime->format('Y/m/d H:i') . '~'
            . '\n 時間：' . $this->order->duration . '時間'
            . '\n クラス：' . $this->order->castClass->name
            . '\n 人数：' . $this->order->total_cast . '人'
            . '\n 場所：' . $this->order->address
            . '\n 獲得ポイント：' . number_format($notifiable->cost * ($startTime->diffInMinutes($endTime) / 30))
            . '\n --------------------------------------------------';

        $room = $notifiable->rooms()
            ->where('rooms.type', RoomType::SYSTEM)
            ->where('rooms.is_active', true)->first();

        $roomMessage = $room->messages()->create([
            'user_id' => 1,
            'type' => MessageType::SYSTEM,
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
        $content = '指名予約が入りました。'
            . '\n 5分以内に承諾、キャンセルの処理を行ってください！';
        $namedUser = 'user_' . $notifiable->id;
        $send_from = UserType::ADMIN;
        $pushId = 'c_2';

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
