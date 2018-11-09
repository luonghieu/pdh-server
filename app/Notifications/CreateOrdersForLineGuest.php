<?php

namespace App\Notifications;

use App\Enums\MessageType;
use App\Enums\OrderType;
use App\Enums\RoomType;
use App\Enums\SystemMessageType;
use App\Order;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;

class CreateOrdersForLineGuest extends Notification implements ShouldQueue
{
    use Queueable;

    public $order;

    /**
     * Create a new notification instance.
     *
     * @param $orderId
     */
    public function __construct($orderId)
    {
        $order = Order::onWriteConnection()->findOrFail($orderId);

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
        return [LineBotNotificationChannel::class];
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
        return [
            //
        ];
    }

    public function lineBotPushData($notifiable)
    {
        $startTime = Carbon::parse($this->order->date . ' ' . $this->order->start_time);
        $endTime = $startTime->copy()->addMinutes($this->order->duration * 60);

        $roomMessage = 'Cheersをご利用いただきありがとうございます！'
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
            'message' => $roomMessage,
            'system_type' => SystemMessageType::NORMAL,
        ]);
        $roomMessage->recipients()->attach($notifiable->id, ['room_id' => $room->id]);

        if ($this->order->type == OrderType::NOMINATION) {
            $content = 'Cheersをご利用いただきありがとうございます！✨'
                . PHP_EOL . 'キャストのご予約を承りました。'
                . PHP_EOL . '--------------------------------------'
                . PHP_EOL . '▼ご予約内容'
                . PHP_EOL . '日時：' . $startTime->format('Y/m/d H:i') . '~'
                . PHP_EOL . '時間：' . $startTime->diffInMinutes($endTime) / 60 . '時間'
                . PHP_EOL . '場所：' . $this->order->address
                . PHP_EOL . PHP_EOL . '現在、キャストの調整を行っております。'
                . PHP_EOL . 'しばらくお待ちください☆'
                . PHP_EOL .  PHP_EOL . '【このあとの流れ】'
                . PHP_EOL .  '①キャストが揃うと、マッチング成功'
                . PHP_EOL .  '②開催予定のお店のURLと予約名を送信'
                . PHP_EOL .  '③マッチング終了後、評価と決済！';
        } else {
            $content = 'Cheersをご利用いただきありがとうございます！✨'
                . PHP_EOL . 'キャストのご予約を承りました。'
                . PHP_EOL . '--------------------------------------'
                . PHP_EOL . '▼ご予約内容'
                . PHP_EOL . '日時：' . $startTime->format('Y/m/d H:i') . '~'
                . PHP_EOL . '時間：' . $startTime->diffInMinutes($endTime) / 60 . '時間'
                . PHP_EOL . 'クラス：' . $this->order->castClass->name
                . PHP_EOL . '人数：' . $this->order->total_cast . '人'
                . PHP_EOL . '場所：' . $this->order->address
                . PHP_EOL . PHP_EOL . '現在、キャストの調整を行っております。'
                . PHP_EOL . 'しばらくお待ちください☆'
                . PHP_EOL .  PHP_EOL . '【このあとの流れ】'
                . PHP_EOL .  '①キャストが揃うと、マッチング成功'
                . PHP_EOL .  '②開催予定のお店のURLと予約名を送信'
                . PHP_EOL .  '③マッチング終了後、評価と決済！';
        }

        return [
            [
                'type' => 'text',
                'text' => $content
            ]
        ];
    }
}
