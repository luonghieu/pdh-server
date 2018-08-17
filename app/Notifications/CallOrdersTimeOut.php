<?php

namespace App\Notifications;

use App\Enums\MessageType;
use App\Enums\RoomType;
use App\Enums\SystemMessageType;
use App\Enums\UserType;
use App\Order;
use App\Services\LogService;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class CallOrdersTimeOut extends Notification implements ShouldQueue
{
    use Queueable;

    public $order;

    /**
     * Create a new notification instance.
     *
     * @param $order
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
        return [PushNotificationChannel::class];
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
        return [];
    }

    public function pushData($notifiable)
    {
        $content = 'ご希望の人数のキャストが揃わなかったため、'
            . PHP_EOL . '下記の予約が無効となりました。'
            . PHP_EOL . '--------------------------------------------------'
            . PHP_EOL . '- キャンセル内容 -'
            . PHP_EOL . '日時：' . Carbon::parse($this->order->date . ' ' . $this->order->start_time)->format('Y/m/d H:i') . '~'
            . PHP_EOL . '時間：' . $this->order->duration . '時間'
            . PHP_EOL . 'クラス：' . $this->order->castClass->name
            . PHP_EOL . '人数：' . $this->order->total_cast . '人'
            . PHP_EOL . '場所：' .  $this->order->address
            . PHP_EOL . '予定合計ポイント：' . number_format($this->order->temp_point) . ' Point'
            . PHP_EOL . '--------------------------------------------------'
            . PHP_EOL . 'お手数ですが、再度コールをし直してください。';

        try {
            $room = $notifiable->rooms()
                ->where('rooms.type', RoomType::SYSTEM)
                ->where('rooms.is_active', true)->first();
            $roomMessage = $room->messages()->create([
                'user_id' => 1,
                'type' => MessageType::SYSTEM,
                'message' => $content,
                'system_type' => SystemMessageType::NORMAL
            ]);
            $roomMessage->recipients()->attach($notifiable->id, ['room_id' => $room->id]);
        } catch (\Exception $e) {
            LogService::writeErrorLog('Room Not Found. User Id:' . $notifiable->id);
            LogService::writeErrorLog($e);
        }


        $namedUser = 'user_' . $notifiable->id;
        $send_from = UserType::ADMIN;
        $pushId = 'g_12';

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
            ],
        ];
    }
}
