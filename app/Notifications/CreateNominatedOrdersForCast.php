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
        $cost = $this->order->castClass->cost;
        $message = 'おめでとう！指名予約が入りました♪'
            . PHP_EOL . '------------------------------------------'
            . PHP_EOL . PHP_EOL . '- ご予約内容 -'
            . PHP_EOL . PHP_EOL . '日時：' . $startTime->format('Y/m/d H:i') . '~'
            . PHP_EOL . PHP_EOL . '時間：' . $this->order->duration . '時間'
            . PHP_EOL . 'クラス：' . $this->order->castClass->name
            . PHP_EOL . '人数：' . $this->order->total_cast . '人'
            . PHP_EOL . '場所：' . $this->order->address
            . PHP_EOL . '獲得ポイント：' . number_format($cost * ($startTime->diffInMinutes($endTime) / 30))
            . PHP_EOL . '--------------------------------------------------';

        $room = $notifiable->rooms()
            ->where('rooms.type', RoomType::SYSTEM)
            ->where('rooms.is_active', true)->first();

        $roomMessage = $room->messages()->create([
            'user_id' => 1,
            'type' => MessageType::SYSTEM,
            'message' => $message
        ]);
        $roomMessage->recipients()->attach($notifiable->id, ['room_id' => $room->id]);

        $content = '指名予約が入りました。'
            . PHP_EOL . '5分以内に承諾、キャンセルの処理を行ってください！';

        return [
            'content' => $content,
            'send_from' => UserType::ADMIN,
        ];
    }

    public function pushData($notifiable)
    {
        $room = $notifiable->rooms()
            ->where('rooms.type', RoomType::SYSTEM)
            ->where('rooms.is_active', true)->first();
        $content = '指名予約が入りました。'
            . PHP_EOL . '5分以内に承諾、キャンセルの処理を行ってください！';
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
                        'room_id' => $room->id
                    ],
                ],
            ],
        ];
    }
}
